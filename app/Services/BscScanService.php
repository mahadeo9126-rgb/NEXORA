<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Exception;

class BscScanService
{
    /**
     * Verify the deposit using Triple-Lock Security Gateway logic.
     * 1. Uniqueness: TxHash does not exist in Transactions table.
     * 2. Ownership: From address matches User Wallet.
     * 3. Target: To address matches Admin Master Wallet.
     * 4. Amount: Hex conversion of transaction value matches required amount.
     *
     * @param string $txHash
     * @param string $walletAddress
     * @param float $expectedAmount
     * @return bool
     * @throws Exception
     */
    public function verifyDeposit(string $txHash, string $walletAddress, float $expectedAmount): bool
    {
        // 1. Uniqueness
        if (Transaction::where('tx_hash', $txHash)->exists()) {
            throw new Exception("Transaction hash already exists.");
        }

        // Call the real BscScan API to verify receipt (tx_status)
        $apiKey = env('BSCSCAN_API_KEY', 'demo');
        $receiptResponse = Http::get("https://api.bscscan.com/api?module=proxy&action=eth_getTransactionReceipt&txhash={$txHash}&apikey={$apiKey}");
        $receiptData = $receiptResponse->json();

        if (!isset($receiptData['result']) || !$receiptData['result']) {
            throw new Exception("Invalid transaction hash or receipt not found.");
        }

        // Check if transaction was successful (status = 1)
        // Note: The receipt returns status as a hex string (e.g., "0x1")
        if (hexdec($receiptData['result']['status']) !== 1) {
            throw new Exception("Transaction failed or is still pending on the blockchain.");
        }

        // Call BscScan API to get transaction details
        $txResponse = Http::get("https://api.bscscan.com/api?module=proxy&action=eth_getTransactionByHash&txhash={$txHash}&apikey={$apiKey}");
        $txData = $txResponse->json();

        if (!isset($txData['result']) || !$txData['result']) {
            throw new Exception("Invalid transaction hash details from BscScan.");
        }

        $txDetails = $txData['result'];

        // 2. Ownership
        if (strtolower($txDetails['from']) !== strtolower($walletAddress)) {
            throw new Exception("Transaction sender does not match registered wallet address.");
        }

        // 3. Contract Lock
        $usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955'; // Official BEP20 USDT
        if (strtolower($txDetails['to']) !== strtolower($usdtContractAddress)) {
            throw new Exception("Invalid token contract. Only official USDT is accepted.");
        }

        // 4. Target
        $adminWallet = config('app.admin_wallet');

        // Handling BEP20 Token Transfers (e.g., USDT)
        // For token transfers, the 'to' address is the contract address.
        // We must check the transaction 'input' data to verify the actual destination address and amount.
        $inputData = $txDetails['input'];

        // Verify it's an ERC20/BEP20 transfer method (0xa9059cbb)
        if (substr($inputData, 0, 10) !== '0xa9059cbb') {
            throw new Exception("Transaction is not a valid BEP20 token transfer.");
        }

        // Extract the destination address (bytes 10 to 74, effectively removing padding)
        $paddedAddress = substr($inputData, 10, 64);
        $destinationAddress = '0x' . substr($paddedAddress, 24); // Remove 24 zeros of padding

        if (strtolower($destinationAddress) !== strtolower($adminWallet)) {
            throw new Exception("Transaction target does not match the Admin Master Wallet.");
        }

        // 5. Amount Verification: Calculate USDT Amount from Hex (18 decimals usually)
        $hexAmount = substr($inputData, 74, 64);
        $decimalAmount = hexdec($hexAmount) / 1e18;

        // Use rounding to 2 decimal places to prevent floating-point math errors
        $roundedReceived = round($decimalAmount, 2);
        $roundedExpected = round($expectedAmount, 2);

        if ($roundedReceived !== $roundedExpected) {
            throw new Exception("Transaction amount ({$roundedReceived} USDT) does not match the expected deposit amount ({$roundedExpected} USDT).");
        }

        return true;
    }
}

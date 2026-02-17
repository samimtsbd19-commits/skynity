<div x-data="{ activeTab: 'general' }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-purple-500 bg-clip-text text-transparent">Settings</h1>
        <p class="text-gray-400">Application Configuration</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-6">
            {{ session('message') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="bg-gray-800/50 backdrop-blur rounded-xl shadow-sm overflow-hidden border border-gray-700/50">
        <div class="border-b">
            <nav class="flex space-x-4 px-4">
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-indigo-600 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-200'" class="py-4 px-2 border-b-2 font-medium text-sm transition-colors">
                    General
                </button>
                <button @click="activeTab = 'voucher'" :class="activeTab === 'voucher' ? 'border-indigo-600 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-200'" class="py-4 px-2 border-b-2 font-medium text-sm transition-colors">
                    Voucher
                </button>
                <button @click="activeTab = 'print'" :class="activeTab === 'print' ? 'border-indigo-600 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-200'" class="py-4 px-2 border-b-2 font-medium text-sm transition-colors">
                    Print
                </button>
                <button @click="activeTab = 'notification'" :class="activeTab === 'notification' ? 'border-indigo-600 text-indigo-400' : 'border-transparent text-gray-400 hover:text-gray-200'" class="py-4 px-2 border-b-2 font-medium text-sm transition-colors">
                    Notifications
                </button>
            </nav>
        </div>

        <!-- General Settings -->
        <div x-show="activeTab === 'general'" class="p-6">
            <div class="max-w-2xl space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">App Name</label>
                    <input type="text" wire:model="appName" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Currency</label>
                        <input type="text" wire:model="currency" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Timezone</label>
                        <select wire:model="timezone" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="Asia/Dhaka">Asia/Dhaka (BST)</option>
                            <option value="UTC">UTC</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Language</label>
                    <select wire:model="language" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                        <option value="bn">Bangla</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <div class="pt-4">
                    <button wire:click="saveGeneral" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Voucher Settings -->
        <div x-show="activeTab === 'voucher'" class="p-6">
            <div class="max-w-2xl space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Voucher Prefix</label>
                    <input type="text" wire:model="voucherPrefix" placeholder="e.g., SKY" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    <p class="text-sm text-gray-400 mt-1">Added before username</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Username Length</label>
                        <input type="number" wire:model="voucherLength" min="4" max="12" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Password Length</label>
                        <input type="number" wire:model="passwordLength" min="4" max="12" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Username Type</label>
                        <select wire:model="usernameType" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="random">Random</option>
                            <option value="sequential">Sequential (001, 002...)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Password Type</label>
                        <select wire:model="passwordType" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="random">Random</option>
                            <option value="same_as_username">Same as username</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4">
                    <button wire:click="saveVoucher" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Print Settings -->
        <div x-show="activeTab === 'print'" class="p-6">
            <div class="max-w-2xl space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Template</label>
                        <select wire:model="printTemplate" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="default">Default</option>
                            <option value="modern">Modern</option>
                            <option value="minimal">Minimal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Columns</label>
                        <select wire:model="printColumns" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                            <option value="2">2 Columns</option>
                            <option value="3">3 Columns</option>
                            <option value="4">4 Columns</option>
                        </select>
                    </div>
                </div>
                <div class="flex space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="showQrCode" class="rounded border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-300">Show QR Code</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="showPrice" class="rounded border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-300">Show Price</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Shop Name</label>
                    <input type="text" wire:model="shopName" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Shop Address</label>
                    <input type="text" wire:model="shopAddress" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Phone Number</label>
                    <input type="text" wire:model="shopPhone" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Footer Text</label>
                    <input type="text" wire:model="footerText" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="pt-4">
                    <button wire:click="savePrint" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div x-show="activeTab === 'notification'" class="p-6">
            <div class="max-w-2xl space-y-6">
                <div class="flex space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="enableEmail" class="rounded border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-300">Email Notifications</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="enableSms" class="rounded border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-300">SMS Notifications</span>
                    </label>
                </div>
                <div x-show="$wire.enableSms" class="space-y-4 p-4 bg-gray-700/30 border border-gray-700/50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">SMS API URL</label>
                        <input type="text" wire:model="smsApiUrl" placeholder="https://sms-provider.com/api/send" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">SMS API Key</label>
                        <input type="password" wire:model="smsApiKey" class="w-full border border-gray-600 rounded-lg px-4 py-2 bg-gray-700/50 text-gray-200 placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="pt-4">
                    <button wire:click="saveNotification" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

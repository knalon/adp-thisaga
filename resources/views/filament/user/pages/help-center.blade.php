<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                <h2 class="text-lg font-semibold">Frequently Asked Questions</h2>
            </x-slot>
            
            <div class="space-y-4">
                <!-- Car Listings FAQ -->
                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button 
                        @click="open = !open" 
                        class="w-full flex justify-between items-center px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                        <span class="font-medium">How do I create a car listing?</span>
                        <x-heroicon-m-chevron-down x-bind:class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 transition-transform" />
                    </button>
                    
                    <div x-show="open" x-collapse class="px-4 py-3 bg-white">
                        <p>To create a car listing:</p>
                        <ol class="list-decimal list-inside mt-2 space-y-1">
                            <li>Navigate to the "My Listings" page from the sidebar</li>
                            <li>Click on the "New Listing" button</li>
                            <li>Fill out all required vehicle details</li>
                            <li>Upload high-quality photos of your vehicle</li>
                            <li>Click "Create" to publish your listing</li>
                        </ol>
                        <p class="mt-2">Your listing will be visible to potential buyers once approved by our team.</p>
                    </div>
                </div>
                
                <!-- Appointments FAQ -->
                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button 
                        @click="open = !open" 
                        class="w-full flex justify-between items-center px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                        <span class="font-medium">How do test drive appointments work?</span>
                        <x-heroicon-m-chevron-down x-bind:class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 transition-transform" />
                    </button>
                    
                    <div x-show="open" x-collapse class="px-4 py-3 bg-white">
                        <p>Test drive appointments allow buyers to schedule time to see and drive a car before making a purchase decision:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Buyers request appointments through the car details page</li>
                            <li>Sellers approve or reject appointment requests</li>
                            <li>Both parties receive notifications about appointment status changes</li>
                            <li>Buyers can include a bid with their appointment request if they wish</li>
                            <li>After the test drive, sellers can mark appointments as completed</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Transactions FAQ -->
                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button 
                        @click="open = !open" 
                        class="w-full flex justify-between items-center px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                        <span class="font-medium">How do I complete a car purchase transaction?</span>
                        <x-heroicon-m-chevron-down x-bind:class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 transition-transform" />
                    </button>
                    
                    <div x-show="open" x-collapse class="px-4 py-3 bg-white">
                        <p>After agreeing on a price with the seller:</p>
                        <ol class="list-decimal list-inside mt-2 space-y-1">
                            <li>The seller creates a transaction in the system</li>
                            <li>You'll receive a notification about the pending transaction</li>
                            <li>Complete the payment as agreed with the seller</li>
                            <li>Once payment is verified, mark the transaction as paid</li>
                            <li>Download your invoice for your records</li>
                        </ol>
                        <p class="mt-2">The car's ownership will be transferred to you upon completion of the transaction.</p>
                    </div>
                </div>
                
                <!-- Account FAQ -->
                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                    <button 
                        @click="open = !open" 
                        class="w-full flex justify-between items-center px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                        <span class="font-medium">How do I update my account information?</span>
                        <x-heroicon-m-chevron-down x-bind:class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 transition-transform" />
                    </button>
                    
                    <div x-show="open" x-collapse class="px-4 py-3 bg-white">
                        <p>To update your account information:</p>
                        <ol class="list-decimal list-inside mt-2 space-y-1">
                            <li>Click on your profile picture in the top-right corner</li>
                            <li>Select "Profile" from the dropdown menu</li>
                            <li>Edit your personal details as needed</li>
                            <li>Click "Save" to update your information</li>
                        </ol>
                        <p class="mt-2">You can also manage your notification preferences and security settings from the profile page.</p>
                    </div>
                </div>
            </div>
        </x-filament::section>
        
        <x-filament::section>
            <x-slot name="heading">
                <h2 class="text-lg font-semibold">Need More Help?</h2>
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="rounded-full bg-primary-500/10 p-3 mr-4">
                            <x-heroicon-o-envelope class="h-6 w-6 text-primary-500" />
                        </div>
                        <h3 class="text-lg font-medium">Contact Support</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Our support team is available Monday-Friday, 9AM-5PM.</p>
                    <x-filament::button tag="a" href="mailto:support@example.com">
                        Email Support
                    </x-filament::button>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="rounded-full bg-warning-500/10 p-3 mr-4">
                            <x-heroicon-o-book-open class="h-6 w-6 text-warning-500" />
                        </div>
                        <h3 class="text-lg font-medium">User Guide</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Download our comprehensive user guide for detailed instructions.</p>
                    <x-filament::button tag="a" href="#" color="warning">
                        Download Guide
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page> 
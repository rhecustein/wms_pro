<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Purchase Code</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Verify Purchase Code
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Please enter your CodeCanyon purchase code
                </p>
            </div>
            
            <form class="mt-8 space-y-6" action="{{ route('installer.purchase.verify') }}" method="POST">
                @csrf
                
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    {{ $errors->first() }}
                                </h3>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="purchase_code" class="sr-only">Purchase Code</label>
                        <input id="purchase_code" name="purchase_code" type="text" required 
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                               placeholder="Purchase Code"
                               value="{{ old('purchase_code') }}">
                    </div>
                    <div>
                        <label for="buyer_email" class="sr-only">Buyer Email</label>
                        <input id="buyer_email" name="buyer_email" type="email" required 
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                               placeholder="Buyer Email"
                               value="{{ old('buyer_email') }}">
                    </div>
                </div>

                <div class="text-sm text-gray-600">
                    <p class="mb-2">Where to find your purchase code?</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Log in to your Envato account</li>
                        <li>Go to Downloads page</li>
                        <li>Click "License certificate & purchase code"</li>
                    </ol>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Verify & Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
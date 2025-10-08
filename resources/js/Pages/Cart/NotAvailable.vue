<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
</script>

<template>
    <Head title="POS - Not Available"/>
    
    <AuthenticatedLayout>
        <template #breadcrumb>
            POS / Cart
        </template>

        <div class="flex flex-wrap">
            <div class="w-full px-4">
                <div class="relative -mt-16 flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-white">
                    <div class="flex flex-col items-center justify-center py-20 px-6">
                        <!-- Icon -->
                        <div class="mb-8">
                            <svg class="w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-gray-800 mb-4">
                            POS / Cart Feature
                        </h1>

                        <!-- Subtitle -->
                        <p class="text-xl text-gray-600 mb-2">
                            Coming Soon
                        </p>

                        <!-- Description -->
                        <p class="text-gray-500 text-center max-w-2xl mb-8">
                            Fitur POS (Point of Sale) / Cart sedang dalam tahap diskusi dan pengembangan. 
                            Fitur ini akan tersedia setelah analisis kebutuhan bisnis dan flow yang sesuai.
                        </p>

                        <!-- Features List -->
                        <div class="bg-gray-50 rounded-lg p-6 max-w-2xl w-full mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Rencana Fitur:</h3>
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-600">Quick product selection dan add to cart</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-600">Real-time stock checking</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-600">Multiple payment methods support</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-600">Custom discount & pricing</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-600">Receipt printing & order history</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-emerald-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-600">Keyboard shortcuts untuk kasir</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Alternative CTA -->
                        <div class="text-center">
                            <p class="text-gray-600 mb-4">
                                Untuk sementara, silakan gunakan:
                            </p>
                            <a 
                                href="/orders/create" 
                                class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md transition duration-150"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Order (Manual)
                            </a>
                        </div>

                        <!-- Contact Info -->
                        <div class="mt-12 text-center">
                            <p class="text-sm text-gray-500">
                                Ada pertanyaan atau saran untuk fitur POS?
                            </p>
                            <p class="text-sm text-gray-400 mt-1">
                                Silakan diskusikan dengan tim development
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<template>

    <Head title="Employee Collaboration" />
    <AuthLayout :breadcrumbs="breadcrumbs">
        <Heading title="Employee Collaboration Analysis" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <label for="csvFile" class="block text-sm font-medium text-gray-700 mb-2">
                Select CSV File
            </label>
            <input type="file" id="csvFile" accept=".csv" @change="handleFileUpload" class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100" />
        </div>

        <div v-if="loading" class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
        </div>

        <div v-if="error" class="bg-red-50 text-red-700 p-4 rounded-md mb-6">
            {{ error }}
        </div>

        <div v-if="collaborations.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee ID #1
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee ID #2
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Project ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Days Worked
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(collab, index) in collaborations" :key="index">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ collab.emp1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ collab.emp2 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ collab.project_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ collab.days_worked }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import AuthLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

// Define collaboration data structure
interface Collaboration {
    emp1: string;
    emp2: string;
    project_id: string;
    days_worked: number;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Employee Collaboration',
        href: '/employee-collaboration',
    },
];

const collaborations = ref<Collaboration[]>([]);
const loading = ref(false);
const error = ref('');

const handleFileUpload = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files) return;

    const file = target.files[0];
    if (!file) return;

    loading.value = true;
    error.value = '';
    collaborations.value = [];

    try {
        const formData = new FormData();
        formData.append('file', file);

        // Get CSRF token from meta tag with proper typing
        const token = (document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;

        const response = await fetch('/employee-collaboration/process', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error('Failed to process file');
        }

        const data = await response.json();
        collaborations.value = data as Collaboration[];
    } catch (err) {
        if (err instanceof Error) {
            error.value = 'Error processing file: ' + err.message;
        } else {
            error.value = 'An unknown error occurred';
        }
        console.error(err);
    } finally {
        loading.value = false;
    }
};
</script>

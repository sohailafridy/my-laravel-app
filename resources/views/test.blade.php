<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div
                    class="p-6 text-gray-900"
                    x-data="cityDropdown(@js($cities))"
                >
                    <form>
                        <div class="mb-4">
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <select
                                id="country"
                                name="country"
                                x-model="selectedCountry"
                                x-on:change="selectedCity = ''"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <select
                                id="city"
                                name="city"
                                x-model="selectedCity"
                                x-bind:disabled="selectedCountry === ''"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:bg-gray-100"
                            >
                                <option value="" x-text="selectedCountry === '' ? 'Select Country first' : 'Select City'"></option>
                                <template x-for="city in filteredCities" :key="city.id">
                                    <option :value="city.id" x-text="city.city_name"></option>
                                </template>
                            </select>
                        </div>

                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cityDropdown(cities) {
            return {
                selectedCountry: '',
                selectedCity: '',
                cities,
                get filteredCities() {
                    return this.cities.filter(city => city.country_id == this.selectedCountry);
                },
            };
        }
    </script>
</x-app-layout>

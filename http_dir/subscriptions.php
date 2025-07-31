<?php include "../resources/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Subscription View</title>
        <style>
            .sub-body {
                background-color: #000123;
                background-attachment: fixed;
                margin: 0;
                padding: 0;
                font-family: 'Inter', sans-serif;
                color: white;
            }

            .sub-container {
                min-width: 100vh;
                padding: 1em;
            }

            .sub-content {
                min-height: calc(100vh - 50px);
                display: flex;
                flex-direction: column;
                align-items: center; /* Center content horizontally */
                gap: 2em; /* Space between sections */
                padding: 1em;
                max-width: 1400px; /* Max width for overall content */
                margin: 0 auto; /* Center the overall content */
            }

            .sub-content h1 {
                font-size: 3em;
                margin-bottom: 0.5em;
                margin-top:0px;
                color: #00bcd4;
                text-shadow: 0 0 10px rgba(0, 188, 212, 0.5);
                width: 100%; /* Ensure title spans full width */
                text-align: center; /* Center the text */
            }

            .filter-container {
                background-color: #2d8bba3a;
                padding: 1.5em 2em;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 188, 212, 0.2);
                display: flex;
                flex-direction: column; /* Main container is column to stack form content and buttons */
                gap: 1.5em; /* Space between form content and buttons */
                width: 100%;
                max-width: 1200px;
            }

            #subscriptionFilterForm {
                width: 100%;
                display: flex; /* Make the form itself a flex container */
                flex-direction: column; /* Stack controls row and buttons vertically */
                gap: 1.5em; /* Gap between controls row and button row */
            }

            .filter-controls-row { /* New wrapper for the two main columns of filters */
                display: flex;
                flex-direction: row; /* Arrange left-column and benefits-column in a row */
                flex-wrap: wrap; /* Allow wrapping on smaller screens */
                gap: 1.5em; /* Space between the two columns */
                justify-content: space-around; /* Distribute columns horizontally */
                align-items: flex-start; /* Align columns to the top */
                width: 100%;
            }

            .left-column-filters { /* Styles for the new left column wrapper */
                display: flex;
                flex-direction: column; /* Stack Platform and Price vertically */
                gap: 1.5em; /* Space between Platform and Price filter groups */
                flex: 1; /* Allow this column to grow */
                min-width: 280px; /* Minimum width for the left column */
                max-width: 350px; /* Limit max width for the left column */
            }

            .filter-group { /* Applies to Platform, Price, and Benefits groups */
                display: flex;
                flex-direction: column;
                gap: 0.8em;
                width: 100%; /* Take full width of its parent (.left-column-filters or .filter-controls-row for benefits) */
            }

            .benefits-column { /* Specific styles for the Benefits column */
                flex: 2; /* Allow benefits to take more space, e.g., double of left column */
                min-width: 350px; /* A bit wider minimum width for benefits */
            }

            .filter-group label {
                font-size: 1.1em;
                color: #00bcd4;
                font-weight: bold;
            }

            .filter-group select,
            .filter-group input[type="range"] {
                width: 100%;
                padding: 0.8em;
                border-radius: 5px;
                border: 1px solid #00bcd4;
                background-color: #2d8bba;
                color: white;
                font-size: 1em;
                -webkit-appearance: none; /* For WebKit browsers */
                appearance: none;
                cursor: pointer;
            }

            .filter-group input[type="range"] {
                width: 320px;
            }

            .filter-group select:focus,
            .filter-group input[type="range"]:focus {
                outline: none;
                box-shadow: 0 0 8px rgba(0, 188, 212, 0.7);
            }

            /* Style for range input thumb */
            input[type="range"]::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                background: #00bcd4;
                cursor: grab;
                box-shadow: 0 0 5px rgba(0, 188, 212, 0.5);
                margin-top: -8px; /* Center thumb vertically */
            }

            input[type="range"]::-moz-range-thumb {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                background: #00bcd4;
                cursor: grab;
                box-shadow: 0 0 5px rgba(0, 188, 212, 0.5);
            }

            /* Style for range input track */
            input[type="range"]::-webkit-slider-runnable-track {
                width: 100%;
                height: 8.4px;
                cursor: pointer;
                background:rgb(21, 21, 65);
                border-radius: 5px;
                border: 1px solid #00bcd4;
            }

            input[type="range"]:focus::-webkit-slider-runnable-track {
                background: #2d8bba3a;
            }

            input[type="range"]::-moz-range-track {
                width: 100%;
                height: 8.4px;
                cursor: pointer;
                background: #2d8bba;
                border-radius: 5px;
                border: 1px solid #00bcd4;
            }

            .price-display {
                text-align: center;
                font-size: 1.1em;
                color: #00bcd4;
                margin-top: 0.5em;
            }

            /* Styles for checkbox group */
            .checkbox-group {
                display: flex;
                flex-wrap: wrap;
                gap: 1em;
                background-color: #2d8bba;
                padding: 1em;
                border-radius: 5px;
                border: 1px solid #00bcd4;
                max-height: 200px; /* Limit height */
                overflow-y: auto; /* Add scroll for many options */
            }

            .checkbox-group label {
                display: flex;
                align-items: center;
                gap: 0.5em;
                cursor: pointer;
                font-size: 1em;
                color: white;
            }

            .checkbox-group input[type="checkbox"] {
                -webkit-appearance: none;
                appearance: none;
                width: 18px;
                height: 18px;
                border: 2px solid #00bcd4;
                border-radius: 4px;
                background-color: #2d8bba;
                cursor: pointer;
                position: relative;
                transition: background-color 0.2s, border-color 0.2s;
            }

            .checkbox-group input[type="checkbox"]:checked {
                background-color: #00bcd4;
                border-color: #00bcd4;
            }

            .checkbox-group input[type="checkbox"]:checked::after {
                content: '✔';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: #1a1a2e;
                font-size: 14px;
                font-weight: bold;
            }

            .filter-buttons {
                display: flex;
                gap: 1em;
                justify-content: flex-end; /* Push buttons to the right */
                width: 100%; /* Take full width of its parent */
            }

            .filter-buttons button {
                padding: 0.8em 1.5em;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1em;
                transition: background-color 0.3s, transform 0.2s;
            }

            .filter-buttons button[type="submit"] {
                background-color: #00bcd4;
                color: #1a1a2e;
            }

            .filter-buttons button[type="submit"]:hover {
                background-color: #00a8c4;
                transform: translateY(-2px);
            }

            .filter-buttons button[type="reset"] {
                background-color: #555;
                color: white;
            }

            .filter-buttons button[type="reset"]:hover {
                background-color: #444;
                transform: translateY(-2px);
            }

            .subscription-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 2em;
                width: 100%;
                max-width: 1200px;
                padding: 1em;
            }

            .subscription-card {
                background-color: #2d8bba3a;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0, 188, 212, 0.2);
                padding: 1.5em;
                display: flex;
                flex-direction: column;
                gap: 0.8em;
                transition: transform 0.2s ease-in-out;
            }

            .subscription-card:hover {
                transform: translateY(-5px);
            }

            .subscription-card h3 {
                color: #00bcd4;
                font-size: 1.6em;
                margin-bottom: 0.4em;
                text-align: center;
            }

            .subscription-card p {
                color: white;
                font-size: 1em;
                margin: 0.2em 0;
            }

            .subscription-card strong {
                color: #00bcd4;
            }

            .subscription-card .benefits-list {
                margin-top: 0.5em;
                padding-left: 1.2em;
                list-style-type: none;
            }

            .subscription-card .benefits-list li::before {
                content: "• ";
                color: #00bcd4;
                font-weight: bold;
                display: inline-block;
                width: 1em;
                margin-left: -1em;
            }

            .subscription-card .price {
                font-size: 1.4em;
                font-weight: bold;
                color: #00bcd4;
                text-align: center;
                margin-top: 1em;
            }

            .subscription-card .official-url {
                display: block;
                text-align: center;
                margin-top: 1em;
            }

            .subscription-card .official-url a {
                display: inline-block;
                background-color: #00bcd4;
                color: #1a1a2e;
                padding: 0.6em 1.2em;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                transition: background-color 0.3s;
            }

            .subscription-card .official-url a:hover {
                background-color: #00a8c4;
            }

            /* Responsive Adjustments */
            @media (max-width: 768px) {
                .sub-content {
                    padding: 0.5em;
                }

                .sub-content h1 {
                    font-size: 2.2em;
                }

                .filter-container {
                    padding: 1em 1.5em;
                }

                .filter-controls-row {
                    flex-direction: column; /* Stack both columns vertically on small screens */
                }

                .left-column-filters,
                .benefits-column {
                    min-width: unset; /* Remove min-width for small screens */
                    max-width: unset; /* Remove max-width for small screens */
                    width: 100%; /* Make them full width */
                }

                .subscription-grid {
                    grid-template-columns: 1fr;
                    padding: 0.5em;
                }
            }
        </style>
    </head>
    <body class="sub-body">
        <div class="sub-container">
            <div class="sub-content">
                <h1>Subscription View</h1>

                <div class="filter-container">
                    <form id="subscriptionFilterForm">
                        <div class="filter-controls-row">
                            <div class="left-column-filters"> <div class="filter-group">
                                    <label for="platform">Platform:</label>
                                    <select id="platform" name="platform">
                                        <option value="">SELECT PLATFORM</option>
                                        </select>
                                </div>

                                <div class="filter-group">
                                    <label for="priceRange">Price Range:</label>
                                    <input type="range" id="priceRange" name="priceRange" min="0" max="160" value="160">
                                    <div class="price-display">Price: $<span id="currentPrice"></span></div>
                                </div>
                            </div> <div class="filter-group benefits-column"> <label>Benefits:</label>
                                <div id="benefits-checkboxes" class="checkbox-group">
                                    </div>
                            </div>
                        </div> 
                    </form>
                </div>

                <div id="subscriptions-list" class="subscription-grid">
                    </div>
            </div>
        </div>

        <script>
            const platformMetadata = {
                "PlayStation": {
                    tiers: ["Basic", "Mid", "Premium"],
                    benefits: [
                        "Ubisoft+ Classics",
                        "PlayStation Plus Game Catalog",
                        "Classics Catalog",
                        "PS5 Streaming",
                        "Game Trials",
                        "Cloud Streaming",
                        "Sony Pictures Catalog"
                    ],
                    maxPrice: 160 // EXAMPLE: Adjust this value based on your actual data
                },
                "Nintendo": {
                    tiers: ["Basic", "Premium"],
                    benefits: [
                        "Nintendo Classics (N64, GBA, GameCube, Sega Genesis)",
                        "Family Plan",
                        "DLC Bundle",
                        "Edition Upgrade"
                    ],
                    maxPrice: 80 // EXAMPLE: Adjust this value based on your actual data
                },
                "Xbox": {
                    tiers: ["Basic", "Mid", "Premium"],
                    benefits: [
                        "100+ Games Library Access",
                        "Game Access on PC and Cloud",
                        "Perks and In-Game Benefits for F2P Games",
                        "DayOne Game Access",
                        "EA Play Membership"
                    ],
                    maxPrice: 25 // EXAMPLE: Adjust this value based on your actual data
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                const platformSelect = document.getElementById('platform');
                const priceRange = document.getElementById('priceRange');
                const currentPriceSpan = document.getElementById('currentPrice');
                const benefitsCheckboxesContainer = document.getElementById('benefits-checkboxes');
                const subscriptionsList = document.getElementById('subscriptions-list');
                const subscriptionFilterForm = document.getElementById('subscriptionFilterForm');
                let debounceTimer;

                // Function to get query parameters from the URL
                function getQueryParam(param) {
                    const urlParams = new URLSearchParams(window.location.search);
                    return urlParams.get(param);
                }

                // Function to get all values for a multi-select parameter (like benefit[])
                function getAllQueryParams(param) {
                    const urlParams = new URLSearchParams(window.location.search);
                    return urlParams.getAll(param);
                }

                // Function to update URL without reloading
                function updateURL(platform, minPrice, maxPrice, selectedBenefits) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('platform', platform);
                    url.searchParams.set('min_price', minPrice);
                    url.searchParams.set('max_price', maxPrice);

                    // Clear existing benefit params before adding new ones
                    url.searchParams.delete('benefit[]');
                    // Add each selected benefit as a separate query parameter
                    selectedBenefits.forEach(benefit => {
                        url.searchParams.append('benefit[]', encodeURIComponent(benefit));
                    });

                    window.history.replaceState({}, '', url.toString());
                }

                function fetchAndDisplaySubscriptions() {
                    const platform = platformSelect.value;
                    const maxPrice = priceRange.value;
                    const minPrice = 0;

                    const selectedBenefits = Array.from(benefitsCheckboxesContainer.querySelectorAll('input[type="checkbox"]:checked'))
                                                .map(checkbox => checkbox.value);

                    const queryParams = new URLSearchParams();
                    if (platform) {
                        queryParams.append('platform', platform);
                    }
                    queryParams.append('min_price', minPrice);
                    queryParams.append('max_price', maxPrice);

                    selectedBenefits.forEach((benefit) => {
                        queryParams.append('benefit[]', encodeURIComponent(benefit));
                    });

                    updateURL(platform, minPrice, maxPrice, selectedBenefits); // Update URL

                    fetch(`../api/SubscriptionView/get_subs_data.php?${queryParams.toString()}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            subscriptionsList.innerHTML = ''; // Clear current subscriptions

                            if (data.success && data.data.length > 0) {
                                data.data.forEach(sub => {
                                    const card = document.createElement('div');
                                    card.className = 'subscription-card';

                                    const benefitsHtml = sub.Benefits ? `
                                        <p><strong>Benefits:</strong></p>
                                        <ul class="benefits-list">
                                            ${sub.Benefits.split(',').map(b => `<li>${b.trim()}</li>`).join('')}
                                        </ul>
                                    ` : '';

                                    card.innerHTML = `
                                        <h3>${sub.SubscriptionName}</h3>
                                        <p><strong>Platform:</strong> ${sub.Platform}</p>
                                        <p><strong>Tier:</strong> ${sub.Tier}</p>
                                        <p><strong>Console:</strong> ${sub.Console}</p>
                                        <p><strong>Duration:</strong> ${sub.Duration} Month</p>
                                        ${benefitsHtml}
                                        <p class="price">Price: $${parseFloat(sub.Price).toFixed(2)}</p>
                                        <div class="official-url">
                                            <a href="${sub.OfficialURL}" target="_blank">Official Website</a>
                                        </div>
                                    `;
                                    subscriptionsList.appendChild(card);
                                });
                            } else {
                                subscriptionsList.innerHTML = `<p style="text-align: center; color: gray;">${data.message}</p>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading subscriptions:', error);
                            subscriptionsList.innerHTML = `<p style="text-align: center; color: red;">Error loading subscriptions: ${error.message}</p>`;
                        });
                }

                function updateFilterOptions(selectedPlatform) {
                    benefitsCheckboxesContainer.innerHTML = ''; // Clear existing checkboxes

                    let currentMaxPrice = 160; // Default global max price

                    if (selectedPlatform && platformMetadata[selectedPlatform]) {
                        const platformInfo = platformMetadata[selectedPlatform];
                        const benefits = platformInfo.benefits;
                        currentMaxPrice = platformInfo.maxPrice; // Get max price for selected platform

                        benefits.forEach(benefit => {
                            const checkboxId = `benefit-${benefit.replace(/[^a-zA-Z0-9]/g, '-')}`; // Create a safe ID
                            const checkboxHtml = `
                                <label for="${checkboxId}">
                                    <input type="checkbox" id="${checkboxId}" name="benefit[]" value="${benefit}">
                                    ${benefit}
                                </label>
                            `;
                            benefitsCheckboxesContainer.insertAdjacentHTML('beforeend', checkboxHtml);
                        });
                    }

                    // Update price range slider max and value
                    priceRange.max = currentMaxPrice;
                    priceRange.value = currentMaxPrice; // Set value to the new max
                    currentPriceSpan.textContent = currentMaxPrice;

                    fetchAndDisplaySubscriptions(); // Re-apply filters after updating options
                }

                // Event Listeners
                platformSelect.addEventListener('change', function() {
                    updateFilterOptions(this.value);
                });

                priceRange.addEventListener('input', function() {
                    currentPriceSpan.textContent = this.value;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(fetchAndDisplaySubscriptions, 300); // Debounce
                });

                // Event delegation for dynamically added checkboxes
                benefitsCheckboxesContainer.addEventListener('change', function(event) {
                    if (event.target.matches('input[type="checkbox"]')) {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(fetchAndDisplaySubscriptions, 300); // Debounce
                    }
                });

                subscriptionFilterForm.addEventListener('reset', function() {
                    // Reset platform dropdown
                    platformSelect.value = '';
                    // Reset price range to default max
                    priceRange.value = 160;
                    priceRange.max = 160; // Also reset max to default
                    currentPriceSpan.textContent = 160;
                    // Clear and re-populate benefits based on empty platform
                    updateFilterOptions(''); // This will also trigger a fetch
                });

                // Prevent default form submission and debounce on submit
                subscriptionFilterForm.addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent default form submission
                    clearTimeout(debounceTimer); // Clear any pending debounced calls
                    fetchAndDisplaySubscriptions();
                });

                // Initial setup on page load
                // Populate platform dropdown first
                const availablePlatforms = Object.keys(platformMetadata);
                availablePlatforms.forEach(platform => {
                    const option = document.createElement('option');
                    option.value = platform;
                    option.textContent = platform;
                    platformSelect.appendChild(option);
                });

                // Set initial platform and load filters from URL or default
                const initialPlatform = getQueryParam('platform');
                if (initialPlatform && availablePlatforms.includes(decodeURIComponent(initialPlatform))) {
                    platformSelect.value = decodeURIComponent(initialPlatform);
                } else {
                    platformSelect.value = ''; // Ensure "SELECT PLATFORM" is chosen if invalid/no param
                }

                // This call will set the correct max price and benefits based on initialPlatform
                updateFilterOptions(platformSelect.value);

                // Re-check benefits from URL after updateFilterOptions has created them
                const initialBenefits = getAllQueryParams('benefit[]');
                if (initialBenefits.length > 0) {
                    Array.from(benefitsCheckboxesContainer.querySelectorAll('input[type="checkbox"]')).forEach(checkbox => {
                        if (initialBenefits.includes(checkbox.value)) {
                            checkbox.checked = true;
                        }
                    });
                    // After setting initial checkboxes, ensure filters are applied (updateFilterOptions already calls fetch, but this ensures re-check if URL has benefits but platform was default)
                    fetchAndDisplaySubscriptions();
                }
            });
        </script>
    </body>
</html>
<?php include "../resources/footer.php"; ?>
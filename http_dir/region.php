<?php include "../resources/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Region View</title>
        <!-- D3.js for data visualization and map rendering -->
        <script src="https://d3js.org/d3.v7.min.js"></script>
        <style>
            /* Basic body and container styles for background and centering */
            .demo-body {
                background-color: #000123;
                background-attachment: fixed;
                margin: 0;
                padding: 0;
                font-family: 'Inter', sans-serif;
                color: white;
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .demo-container {
                flex-grow: 1; /* Allow container to take available space */
                display: flex;
                flex-direction: column;
                align-items: center; /* Center content horizontally */
                padding: 1em;
            }

            .demo-content {
                width: 100%;
                max-width: 1400px; /* Max width for overall content */
                text-align: center;
                min-height: calc(100vh - 50px); /* Adjust based on header/footer height */
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1em;
            }

            .demo-content h1 {
                font-size: 3em;
                margin-bottom: 0.5em;
                color: #00bcd4;
                text-shadow: 0 0 10px rgba(0, 188, 212, 0.5);
            }

            .demo-visual {
                width: 100%;
                flex-grow: 1; /* Allow map container to expand */
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #2d8bba3a; /* Semi-transparent background for visual area */
                border-radius: 15px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
                min-height: 500px; /* Minimum height for the map */
                margin: 0px 0px 50px 0px; /* From your provided file */
                padding: 0px 0px 0px 0px; /* From your provided file */
            }

            .demo-map {
                width: 100%;
                height: 100%;
                display: flex; /* Use flex to center SVG */
                justify-content: center;
                align-items: center;
            }

            /* Tooltip Styling */
            .tooltip-map {
                position: absolute;
                background-color: rgba(45, 139, 186, 0.9); /* Slightly darker, more opaque */
                border: 1px solid #00bcd4;
                border-radius: 10px; /* Rounded corners */
                padding: 10px;
                text-align: center;
                pointer-events: none; /* Allows mouse events to pass through */
                opacity: 0;
                transition: opacity 0.2s ease;
                z-index: 1000; /* Ensure tooltip is on top */
                max-width: 300px; /* Limit tooltip width */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            }

            .tooltip-map h4 {
                margin: 0 0 5px 0;
                color: white;
                font-size: 1.2em;
            }

            .tooltip-map .pie-chart-container {
                width: 250px; /* Adjusted from 300px to better fit common tooltip sizes */
                height: 250px; /* Adjusted from 300px */
                margin: 5px auto 0; /* Center the pie chart, reduced top margin */
                background-color: transparent; /* Ensure background is clear */
            }

            .tooltip-map .pie-chart-label {
                font-size: 14px; /* Adjusted from 14px for better fit */
                font-weight: bold;
                fill: white; /* White text for labels */
                text-anchor: middle;
            }

            .tooltip-map .no-data-message {
                color: #ffeb3b; /* Yellow for warnings */
                font-size: 0.9em;
                margin-top: 5px;
            }

            /* Spinner for loading state */
            .spinner {
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top: 4px solid #00bcd4;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                animation: spin 1s linear infinite;
                margin: 10px auto;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

        </style>
    </head>
    <body class="demo-body">
        <div class="demo-container">  
            <main class="demo-content">
                <h1>Region</h1>
                <div class="demo-visual">
                    <div id="demo-map">
                        <!-- D3.js map will be rendered here -->
                    </div>
                </div>
            </main>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', async function() {
                const mapContainer = d3.select("#demo-map");
                const width = mapContainer.node().getBoundingClientRect().width;
                const height = mapContainer.node().getBoundingClientRect().height;

                // Adjust width/height for responsiveness if they are 0 initially
                const effectiveWidth = width > 0 ? width : window.innerWidth * 0.8; // Default to 80% of window if no width
                const effectiveHeight = height > 0 ? height : window.innerHeight * 0.7; // Default to 70% of window if no height

                // Clear the "Loading map..." message
                mapContainer.html(''); 

                // Create SVG for the map
                const svg = mapContainer.append("svg")
                    .attr("width", "100%") // Make SVG responsive to parent
                    .attr("height", 800) // Keep your requested fixed height
                    .attr("viewBox", `0 0 ${effectiveWidth} ${effectiveHeight}`) // Maintain aspect ratio
                    .attr("preserveAspectRatio", "xMidYMid meet");

                // Define map projection
                const projection = d3.geoNaturalEarth1()
                    .scale(effectiveWidth / 5.5) // Adjust scale based on effective width
                    .translate([effectiveWidth / 2, effectiveHeight / 2]);

                const path = d3.geoPath().projection(projection);

                // Create a tooltip div
                const tooltip = d3.select("body").append("div")
                    .attr("class", "tooltip-map")
                    .style("opacity", 0);

                // Define the regions based on your database and desired country groupings
                const regionMapping = {
                    "North America": [
                        "Canada", "United States of America", "Mexico",
                        "Cuba", "Haiti", "Dominican Republic", "Jamaica", "Puerto Rico",
                        "Guatemala", "Belize", "El Salvador", "Honduras", "Nicaragua",
                        "Costa Rica", "Panama"
                    ],
                    "Europe": [
                        "United Kingdom", "France", "Germany", "Italy", "Spain", "Portugal",
                        "Ireland", "Netherlands", "Belgium", "Switzerland", "Austria",
                        "Sweden", "Norway", "Finland", "Denmark", "Poland", "Ukraine",
                        "Russia", // Part of Russia is in Europe
                        "Greece", "Turkey", // Part of Turkey is in Europe
                        "Hungary", "Czechia", "Slovakia", "Romania", "Bulgaria",
                        "Serbia", "Croatia", "Bosnia and Herzegovina", "Montenegro",
                        "Albania", "North Macedonia", "Slovenia", "Luxembourg",
                        "Iceland", "Greenland" // Culturally often grouped with Europe
                    ],
                    "Japan": ["Japan"],
                    "Others": [] // Countries not explicitly listed will fall into "Others"
                };

                // Invert the regionMapping for quick lookup from country to region name
                const countryToRegionMap = new Map();
                for (const region in regionMapping) {
                    if (regionMapping[region].length > 0) {
                        regionMapping[region].forEach(country => {
                            countryToRegionMap.set(country, region);
                        });
                    }
                }

                // Define colors for each region
                const regionColors = {
                    "North America": "red",
                    "Europe": "blue",
                    "Japan": "yellow",
                    "Others": "green"
                };
                
                // Define custom colors for pie chart platforms
                const platformColors = {
                    "PlayStation": "#3366FF", // Blue
                    "Nintendo": "#FF3333",    // Red
                    "Xbox": "#33FF33"         // Green
                };

                let currentHighlightedRegion = null;

                // Function to get the region name from a country name
                function getRegionFromCountryName(countryName) {
                    return countryToRegionMap.get(countryName) || "Others";
                }

                // Function to render the pie chart inside the tooltip
                function renderPieChart(data, parentElement) {
                    // Clear previous content
                    parentElement.html('');

                    if (!data || data.length === 0) {
                        parentElement.append("p")
                            .attr("class", "no-data-message")
                            .text("No sales data available for this region.");
                        return;
                    }

                    const pieWidth = 250, pieHeight = 250, pieRadius = Math.min(pieWidth, pieHeight) / 2;

                    const pieSvg = parentElement.append("svg")
                        .attr("width", pieWidth)
                        .attr("height", pieHeight)
                        .append("g")
                        .attr("transform", `translate(${pieWidth / 2}, ${pieHeight / 2})`);

                    const pie = d3.pie()
                        .sort(null)
                        .value(d => d.TotalSales);

                    const arc = d3.arc()
                        .innerRadius(0)
                        .outerRadius(pieRadius);

                    // Use the custom platformColors object for the pie chart
                    const colorScale = d3.scaleOrdinal()
                        .domain(Object.keys(platformColors))
                        .range(Object.values(platformColors));

                    const arcs = pieSvg.selectAll(".arc")
                        .data(pie(data))
                        .enter().append("g")
                        .attr("class", "arc");

                    arcs.append("path")
                        .attr("d", arc)
                        .attr("fill", d => colorScale(d.data.Platform)); // Use platformColors here!

                    // Add text labels
                    arcs.append("text")
                        .attr("transform", d => `translate(${arc.centroid(d)})`)
                        .attr("dy", "1px")
                        .attr("class", "pie-chart-label")
                        .text(d => {
                            const percentage = ((d.data.TotalSales / d3.sum(data, x => x.TotalSales)) * 100).toFixed(1);
                            return `${d.data.Platform}: ${percentage}%`;
                        });
                }

                // Load GeoJSON data
                try {
                    const world = await d3.json("../api/RegionView/custom.geo.json");
                    
                    // Filter features to include only polygons (countries)
                    const countries = world.features.filter(d => d.geometry.type === "Polygon" || d.geometry.type === "MultiPolygon");

                    // Assign regions to countries and draw them
                    const countryPaths = svg.selectAll("path")
                        .data(countries)
                        .enter().append("path")
                        .attr("class", "country")
                        .attr("d", path)
                        .attr("fill", d => regionColors[getRegionFromCountryName(d.properties.name_en)] || regionColors["Others"]) // Set initial fill color by region
                        .each(function(d) {
                            // Store the region name on the DOM element for easy lookup during hover
                            d.properties.region = getRegionFromCountryName(d.properties.name_en);
                        });

                    // Mouseover event handler
                    countryPaths.on("mouseover", async function(event, d) {
                        const currentRegion = d.properties.region;
                        
                        // Highlight all countries in the same region
                        svg.selectAll(".country")
                           .filter(p => p.properties.region === currentRegion)
                           .style("fill", d => d3.color(regionColors[currentRegion]).darker(0.8)); // Darken the original region color

                        // Update current highlighted region
                        currentHighlightedRegion = currentRegion;

                        tooltip.html(`
                            <h4>${currentRegion}</h4>
                            <div class="pie-chart-container">
                                <div class="spinner"></div>
                                <p class="no-data-message">Loading sales data...</p>
                            </div>
                        `)
                        .style("opacity", 1);

                        try {
                            const response = await fetch(`../api/RegionView/get_region_data.php?region_name=${encodeURIComponent(currentRegion)}`);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            const data = await response.json();

                            const pieChartContainer = tooltip.select(".pie-chart-container");
                            pieChartContainer.html(''); // Clear spinner/loading message

                            if (data.success && data.data.length > 0) {
                                renderPieChart(data.data, pieChartContainer);
                            } else {
                                pieChartContainer.append("p")
                                    .attr("class", "no-data-message")
                                    .text(data.message || "No sales data available for this region.");
                            }
                        } catch (error) {
                            console.error("Error fetching region data:", error);
                            const pieChartContainer = tooltip.select(".pie-chart-container");
                            pieChartContainer.html(''); // Clear spinner/loading message
                            pieChartContainer.append("p")
                                .attr("class", "no-data-message")
                                .text(`Error: ${error.message}.`);
                        }
                    })
                    .on("mousemove", function(event) {
                        // Get tooltip dimensions
                        const tooltipWidth = tooltip.node().offsetWidth;
                        const tooltipHeight = tooltip.node().offsetHeight;

                        // Get window dimensions
                        const windowWidth = window.innerWidth;
                        const windowHeight = window.innerHeight;

                        let left = event.pageX + 15; // Default to right of cursor
                        let top = event.pageY - tooltipHeight - 15; // Default to above cursor

                        // Adjust left position if tooltip goes off right edge
                        if (left + tooltipWidth > windowWidth - 20) { // 20px margin from right edge
                            left = event.pageX - tooltipWidth - 15; // Position to the left of cursor
                        }

                        // Adjust top position if tooltip goes off top edge
                        if (top < 10) { // 10px margin from top edge
                            top = event.pageY + 20; // Position below cursor
                        }

                        tooltip.style("left", left + "px")
                               .style("top", top + "px");
                    })
                    .on("mouseout", function(event, d) {
                        // Only revert colors if the mouse moves OUT of the current highlighted region
                        const newTargetRegion = getRegionFromCountryName(event.relatedTarget?.properties?.name_en);

                        if (currentHighlightedRegion && newTargetRegion !== currentHighlightedRegion) {
                            svg.selectAll(".country")
                               .filter(p => p.properties.region === currentHighlightedRegion)
                               .style("fill", p => regionColors[p.properties.region] || regionColors["Others"]); // Revert to original region color
                            currentHighlightedRegion = null;
                            tooltip.style("opacity", 0); // Hide tooltip
                        } else if (!event.relatedTarget || !event.relatedTarget.tagName || event.relatedTarget.tagName.toLowerCase() !== 'path') {
                            // If related target is not a country path (e.g., leaving SVG area)
                            svg.selectAll(".country")
                               .style("fill", p => regionColors[p.properties.region] || regionColors["Others"]); // Revert all colors
                            currentHighlightedRegion = null;
                            tooltip.style("opacity", 0); // Hide tooltip
                        }
                    });

                } catch (error) {
                    console.error("Error loading GeoJSON data:", error);
                    mapContainer.html('<p style="color: red; text-align: center;">Error loading map data. Please check console for details.</p>');
                }

                // Handle window resize for responsiveness
                window.addEventListener('resize', () => {
                    const newWidth = mapContainer.node().getBoundingClientRect().width;
                    const newHeight = mapContainer.node().getBoundingClientRect().height;

                    if (newWidth > 0 && newHeight > 0) {
                        svg.attr("viewBox", `0 0 ${newWidth} ${newHeight}`);
                        // Re-calculate projection scale and translate based on new dimensions
                        projection.scale(newWidth / 5.5).translate([newWidth / 2, newHeight / 2]);
                        svg.selectAll("path").attr("d", path);
                    }
                });
            });
        </script>
    </body>
</html>
<?php include "../resources/footer.php"; ?>
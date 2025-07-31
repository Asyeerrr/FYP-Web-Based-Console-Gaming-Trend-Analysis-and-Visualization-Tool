<?php include "../resources/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Console View</title>
        <style>
            .console-body {
                background-color: #000123;
                background-attachment: fixed;
                margin: 0;
                padding: 0;
                font-family: 'Inter', sans-serif; /* Ensure a consistent font */
            }

            .console-container {
                min-width: 100vh;
            }

            .console-content {
                min-height: calc(100vh - 50px);
                display: flex;
                justify-content: space-between;
                gap: 1em;
                padding: 1em;
                flex-wrap: wrap; /* Allow wrapping on smaller screens */
            }

            .console-left {
                display: flex;
                flex-direction: column;
                flex: 1; /* Equal flex distribution for now */
                min-width: 45%; /* Ensure minimum width before wrapping */
                gap: 1em; /* Space between inner elements */
            }

            .console-right {
                display: flex;
                flex-direction: column; /* Stack top-rated and genre vertically */
                flex: 1; /* Equal flex distribution for now */
                min-width: 45%; /* Ensure minimum width before wrapping */
                gap: 1em; /* Space between inner elements */
            }


            @media (max-width: 768px) {
                .console-left,
                .console-right {
                    flex: 1 1 100%; /* Stack vertically on small screens */
                    min-width: unset;
                }
            }


            /*
            .console-upper-right,
            .console-lower-right{
                display: flex;
                justify-content: space-between;
                gap: 1em;
                flex-wrap: wrap;
            }
            @media (max-width: 480px) {
                .console-upper-right,
                .console-lower-right {
                    flex-direction: column;
                }
            }
            */


            .top-rated-games,
            .genre-distribution,
            .console-price,
            .esrb-rating {
                display: flex; /* Use flex for internal alignment within these boxes */
                flex-direction: column;
                /* flex: 1;  Distribute space equally - now handled by parent flex containers */
                text-align: center;
                color: white;
                background-color: #2d8bba3a;
                padding-bottom: 20px;
                border-radius: 10px;
                font-size: 20px;
                box-sizing: border-box; /* Include padding in flex basis */
                min-height: 200px; /* Example min-height */
            }

            /* Specific heights / flex for individual components */
            .top-rated-games {
                min-height: 350px; /* Adjusted for vertical bar chart */
                flex: 1; /* Take available space in console-right */
            }
            .genre-distribution {
                min-height: 350px; /* Adjusted for pie chart */
                flex: 1; /* Take available space in console-right */
            }
            .console-price {
                width: 100%; /* Make it span full width when it wraps */
                min-height: 350px; /* Increased height for line chart better visibility */
                margin-top: 1em; /* Add some space above it */
            }
            .esrb-rating {
                min-height: 250px; /* Example height for ESRB chart */
                /* flex: none; Let it take its natural height within console-left */
            }


            .console-title {
                text-align: left;
                font-size: 30px;
                color: white;
                margin-bottom: 1.1em;
            }

            .console-title h1 {
                margin-top: 0px;
                margin-bottom: 0px;
            }

            .console-title select {
                color: white;
                background-color: #2d8bba;
                border-radius: 5px;
                padding: 5px;
                font-size: 20px;
                font-weight: bold;
            }

            .console-details {
                text-align: left; /* Align text to left for details */
                color: white;
                background-color: #2d8bba3a;
                padding: 20px;
                border-radius: 10px;
                font-size: 16px; /* Smaller font for details */
                display: flex;
                flex-direction: column;
                gap: 10px; /* Space between detail lines */
                min-height: 300px; /* Ensure enough space for details */
                flex: 1; /* Allow it to grow */
            }
            .console-details h4 {
                text-align: center; /* Center the title "Console Details" */
                margin-top: 0;
                margin-bottom: 15px;
                font-size: 24px;
            }
            .console-details p {
                margin: 0; /* Remove default paragraph margins */
            }
            .console-details strong {
                color: #ADD8E6; /* Light blue for key details */
            }
            /* Styling for the console image within the details card */
            .console-details #console-image-container {
                text-align: center; /* Center the image */
                margin-top: 15px;
                margin-bottom: 15px; /* Add margin below image */
            }
            .console-details #console-image-container img {
                max-width: 250px; /* Adjust max-width to control image size better */
                height: auto;
                border-radius: 8px; /* Optional: subtle rounded corners for the image */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: subtle shadow */
            }

            /* Styles for D3.js charts */
            .x-axis text, .y-axis text {
                fill: white;
                font-size: 12px;
            }
            .x-axis path, .y-axis path,
            .x-axis line, .y-axis line {
                stroke: white;
            }
            .bar {
                fill: steelblue;
                transition: fill 0.3s ease;
            }
            .bar-label {
                fill: white;
                font-size: 10px; /* Smaller font for game titles */
                text-anchor: middle;
            }
            /* Styles for pie chart labels */
            .pie-label {
                fill: white;
                font-size: 14px;
            }
            /* Styles for line chart */
            .line {
                fill: none;
                stroke: steelblue;
                stroke-width: 2px;
            }
            .dot {
                fill: steelblue;
                stroke: #fff;
                stroke-width: 0.5px;
            }
            /* Tooltip for line chart */
            .price-tooltip {
                position: absolute;
                text-align: center;
                padding: 8px;
                font: 12px sans-serif;
                background: lightsteelblue;
                border: 0px;
                border-radius: 8px;
                pointer-events: none;
                color: black;
            }


         </style>
        <script src="https://d3js.org/d3.v7.min.js"></script>
    </head>
    <body class="console-body">
        <div class="console-container">

            <main class="console-content">

                <div class="console-left">

                    <div class="console-title">
                        <h1>Console</h1>
                        <label for="console-select"></label>
                        <select name="console" id="console-select">
                            <option value="">SELECT CONSOLE</option>
                            <option value="PlayStation">PlayStation</option>
                            <option value="PlayStation 2">PlayStation 2</option>
                            <option value="PlayStation 3">PlayStation 3</option>
                            <option value="PlayStation 4">PlayStation 4</option>
                            <option value="PlayStation 5">PlayStation 5</option>
                            <option value="PlayStation Portable">PlayStation Portable</option>
                            <option value="PlayStation Vita">PlayStation Vita</option>
                            <option value="Nintendo Entertainment System (NES)">Nintendo Entertainment System</option>
                            <option value="Super Nintendo Entertainment System (SNES)">Super Nintendo Entertainment System</option>
                            <option value="Nintendo 64 (N64)">Nintendo 64</option>
                            <option value="Game Boy">Game Boy</option>
                            <option value="Game Boy Advance">Game Boy Advance</option>
                            <option value="GameCube">GameCube</option>
                            <option value="Nintendo DS">Nintendo DS</option>
                            <option value="Nintendo 3DS">Nintendo 3DS</option>
                            <option value="Nintendo Wii">Nintendo Wii</option>
                            <option value="Nintendo Wii U">Nintendo Wii U</option>
                            <option value="Nintendo Switch">Nintendo Switch</option>
                            <option value="Xbox">Xbox</option>
                            <option value="Xbox 360">Xbox 360</option>
                            <option value="Xbox One">Xbox One</option>
                            <option value="Xbox Series X">Xbox Series X</option>
                            <option value="Xbox Series S">Xbox Series S</option>
                        </select>

                    </div>

                    <div class="console-details" style="margin: 0px 0px 0px 0px; padding-bottom: 70px">
                        </div>

                    <div class="esrb-rating" style="flex-direction:column; margin-bottom:0px;">
                        <h4 style="height:100px;">ESRB Rating Distribution</h4>
                        </div>

                </div>

                <div class="console-right">

                    <div class="top-rated-games">
                        <h4>Top Rated Games</h4>
                        </div>

                    <div class="genre-distribution" style="margin: 0px 0px 0px 0px;">
                        <h4>Genre Distribution</h4>
                        </div>

                </div>

                <div class="console-price" style="margin: 0px 0px 0px 0px;">
                    <h4>Console Price</h4>
                    </div>

            </main>


        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const consoleSelect = document.getElementById('console-select');
                const consoleDetailsContainer = document.querySelector('.console-details');
                // Removed consoleImageContainer global as it's now part of detailsHtml


                // Function to get query parameter by name
                function getQueryParam(name) {
                    const urlParams = new URLSearchParams(window.location.search);
                    return urlParams.get(name);
                }

                // Function to load console details
                function loadConsoleDetails(consoleName) {
                    if (!consoleName) {
                        consoleDetailsContainer.innerHTML = '<h4>Console Details</h4><p>Please select a console to view its details.</p>';
                        return;
                    }

                    // Clear previous details and loading message
                    consoleDetailsContainer.innerHTML = '<h4>Console Details</h4><p>Loading...</p>';

                    const apiUrl = `../api/ConsoleView/get_console_details.php?console_name=${encodeURIComponent(consoleName)}`;

                    fetch(apiUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.data) {
                                const details = data.data;
                                // Clear loading message
                                consoleDetailsContainer.innerHTML = '<h4>Console Details</h4>';

                                // Add details and image to the card
                                const detailsHtml = `
                                    <div id="console-image-container">
                                        <img src="${details.ImageURL}" alt="${details.ConsoleName}" onerror="this.onerror=null;this.src='https://placehold.co/200x200?text=Image+Not+Found';" />
                                    </div>
                                    <p><strong>Console Name:</strong> ${details.ConsoleName}</p>
                                    <p><strong>Publisher:</strong> ${details.Publisher}</p>
                                    <p><strong>Released:</strong> ${details.Released}</p>
                                    <p><strong>Discontinued:</strong> ${details.Discontinued}</p>
                                    <p><strong>Original Price:</strong> $${parseFloat(details.OriginalPrice).toFixed(2)}</p>
                                    <p><strong>Current Price:</strong> $${parseFloat(details.CurrentPrice).toFixed(2)}</p>
                                    <p><strong>Generation:</strong> ${details.Generation}</p>
                                `;
                                consoleDetailsContainer.insertAdjacentHTML('beforeend', detailsHtml);

                            } else {
                                consoleDetailsContainer.innerHTML = `<h4>Console Details</h4><p style="color: red;">${data.message || 'No details found for this console.'}</p>`;
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching console details:", error);
                            consoleDetailsContainer.innerHTML = `<h4>Console Details</h4><p style="color: red;">Error loading details: ${error.message}</p>`;
                        });

                    // Load charts dependent on consoleName
                    loadTopRatedGamesChart(consoleName);
                    loadGenreDistributionChart(consoleName); // Call to load the new chart
                    loadESRBRatingChart(consoleName); // Call to load the ESRB chart
                    loadConsolePriceChart(consoleName); // Call to load the Console Price chart
                }

                // --- Top Rated Games Vertical Bar Chart ---
                function loadTopRatedGamesChart(consoleName) {
                    const topRatedGamesContainer = d3.select(".top-rated-games");
                    // Clear any previous chart content
                    topRatedGamesContainer.selectAll("svg").remove();
                    topRatedGamesContainer.selectAll("p").remove(); // Remove old messages

                    if (!consoleName) {
                        topRatedGamesContainer.append("p").text("Select a console to see top rated games.").style("color", "white").style("text-align", "center");
                        return;
                    }

                    // Append loading message
                    topRatedGamesContainer.append("p").text("Loading top rated games...").style("color", "white").style("text-align", "center");


                    const chartWidth = topRatedGamesContainer.node().getBoundingClientRect().width - 10;
                    const chartHeight = 700; // Adjusted height for a vertical bar chart

                    const margin = { top: 20, right: 20, bottom: 450, left: 60 }; // Increased bottom margin for rotated labels
                    const width = chartWidth - margin.left - margin.right;
                    const height = chartHeight - margin.top - margin.bottom;

                    const svg = topRatedGamesContainer.append("svg")
                        .attr("width", chartWidth)
                        .attr("height", chartHeight)
                        .append("g")
                        .attr("transform", `translate(${margin.left}, ${margin.top})`);

                    const apiUrl = `../api/ConsoleView/get_rated_games.php?console_name=${encodeURIComponent(consoleName)}`;

                    fetch(apiUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            topRatedGamesContainer.selectAll("p").remove(); // Remove loading message


                            if (data.success && data.data.length > 0) {
                                const gamesData = data.data;

                                // X scale for game names
                                const xScale = d3.scaleBand()
                                    .domain(gamesData.map(d => d.Name))
                                    .range([0, width])
                                    .padding(0.1);

                                // Y scale for ratings (assuming 0-100 or 0-5 scale for ratings)
                                // Adjust domain based on your actual rating scale
                                const yScale = d3.scaleLinear()
                                    .domain([4.0, 5.0]) // Add a small buffer for top label
                                    .range([height, 0]); // Inverted for vertical bars

                                // X-axis (game names)
                                svg.append("g")
                                    .attr("class", "x-axis")
                                    .attr("transform", `translate(0, ${height})`)
                                    .call(d3.axisBottom(xScale))
                                    .selectAll("text")
                                    .attr("fill", "white")
                                    .attr("transform", "rotate(90)") // Rotate labels for better readability
                                    .style("text-anchor", "start")  // Anchor to the end of the rotated text
                                    .attr("x", 10)
                                    .attr("y", -5);

                                // Y-axis (ratings)
                                svg.append("g")
                                    .attr("class", "y-axis")
                                    .call(d3.axisLeft(yScale))
                                    .selectAll("text")
                                    .attr("fill", "white");

                                // Add Y-axis label
                                svg.append("text")
                                    .attr("transform", "rotate(-90)")
                                    .attr("y", 0 - margin.left + 5)
                                    .attr("x", 0 - (height / 2))
                                    .attr("dy", "1em")
                                    .style("text-anchor", "middle")
                                    .style("fill", "white")
                                    .style("font-size", "14px")
                                    .text("Rating");

                                // Draw bars
                                svg.selectAll(".bar")
                                    .data(gamesData)
                                    .enter()
                                    .append("rect")
                                    .attr("class", "bar")
                                    .attr("x", d => xScale(d.Name))
                                    .attr("y", d => yScale(parseFloat(d.Rating)))
                                    .attr("width", xScale.bandwidth())
                                    .attr("height", d => height - yScale(parseFloat(d.Rating)))
                                    .attr("fill", "#00bcd4") // A nice blue for bars
                                    .on("mouseover", function() {
                                        d3.select(this).attr("fill", "#80deea"); // Lighten on hover
                                    })
                                    .on("mouseout", function() {
                                        d3.select(this).attr("fill", "#00bcd4"); // Revert color
                                    });

                                // Add rating labels on top of bars
                                svg.selectAll(".bar-value")
                                    .data(gamesData)
                                    .enter()
                                    .append("text")
                                    .attr("class", "bar-value")
                                    .attr("x", d => xScale(d.Name) + xScale.bandwidth() / 2)
                                    .attr("y", d => yScale(parseFloat(d.Rating)) - 5) // Position slightly above bar
                                    .attr("text-anchor", "middle")
                                    .attr("fill", "white")
                                    .style("font-size", "10px")
                                    .text(d => parseFloat(d.Rating).toFixed(1)); // Format rating to one decimal

                            } else {
                                topRatedGamesContainer.append("p").text(`No top rated games found for ${consoleName}.`).style("color", "white").style("text-align", "center");
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching top rated games data:", error);
                            topRatedGamesContainer.append("p").text(`Error loading top rated games: ${error.message}`).style("color", "red").style("text-align", "center");
                        });
                }

                // --- Genre Distribution Pie Chart ---
                function loadGenreDistributionChart(consoleName) {
                    const genreDistributionContainer = d3.select(".genre-distribution");
                    // Clear any previous chart content
                    genreDistributionContainer.selectAll("svg").remove();
                    genreDistributionContainer.selectAll("p").remove(); // Remove old messages

                    if (!consoleName) {
                        genreDistributionContainer.append("p").text("Select a console to see genre distribution.").style("color", "white").style("text-align", "center");
                        return;
                    }

                    // Append loading message
                    genreDistributionContainer.append("p").text("Loading genre distribution...").style("color", "white").style("text-align", "center");

                    const chartWidth = genreDistributionContainer.node().getBoundingClientRect().width;
                    const chartHeight = 400; // Keep height consistent for pie chart

                    const margin = { top: 0, right: 20, bottom: 20, left: 20 };
                    const width = chartWidth - margin.left - margin.right;
                    const height = chartHeight - margin.top - margin.bottom;
                    const radius = Math.min(width, height) / 2 - 20; // Radius for the pie chart

                    const svg = genreDistributionContainer.append("svg")
                        .attr("width", chartWidth)
                        .attr("height", chartHeight)
                        .append("g")
                        .attr("transform", `translate(${chartWidth / 2}, ${chartHeight / 2 + margin.top / 2})`);

                    const color = d3.scaleOrdinal(d3.schemeCategory10); // D3's built-in color scheme

                    const pie = d3.pie()
                        .value(d => d.count)
                        .sort(null); // No sorting, rely on API's sort or natural order

                    const arc = d3.arc()
                        .innerRadius(0)
                        .outerRadius(radius);

                    const outerArc = d3.arc()
                        .innerRadius(radius * 1.1)
                        .outerRadius(radius * 1.1);

                    const apiUrl = `../api/ConsoleView/get_genre_console.php?console_name=${encodeURIComponent(consoleName)}`;

                    fetch(apiUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            genreDistributionContainer.selectAll("p").remove(); // Remove loading message

                            if (data.success && data.data.length > 0) {
                                let genreData = data.data; // This data is already sorted DESC by count from PHP

                                const topN = 5;
                                let processedGenreData = [];
                                let otherCount = 0;

                                // Separate top N genres and sum the rest into 'Others'
                                if (genreData.length > topN) {
                                    processedGenreData = genreData.slice(0, topN); // Take the top N
                                    for (let i = topN; i < genreData.length; i++) {
                                        otherCount += genreData[i].count;
                                    }
                                    // Add 'Others' category if there are remaining genres
                                    if (otherCount > 0) {
                                        processedGenreData.push({ genre: "Others", count: otherCount });
                                    }
                                } else {
                                    // If less than or equal to N genres, show all of them
                                    processedGenreData = genreData;
                                }

                                const totalCount = d3.sum(genreData, d => d.count); // Calculate total from processed data
                                const arcs = pie(processedGenreData); // Use processed data for arcs

                                // Draw pie slices
                                svg.selectAll("slices")
                                    .data(arcs)
                                    .enter()
                                    .append("path")
                                    .attr("d", arc)
                                    .attr("fill", d => color(d.data.genre))
                                    .attr("stroke", "white")
                                    .style("stroke-width", "1px")
                                    .on("mouseover", function(event, d) {
                                        d3.select(this).transition()
                                            .duration(100)
                                            .attr("d", d3.arc().innerRadius(0).outerRadius(radius * 1.05)); // Slight grow effect
                                    })
                                    .on("mouseout", function(event, d) {
                                        d3.select(this).transition()
                                            .duration(100)
                                            .attr("d", arc); // Revert to original size
                                    });

                                // Add text labels
                                svg.selectAll("labels")
                                    .data(arcs)
                                    .enter()
                                    .append("text")
                                    .attr("transform", d => `translate(${outerArc.centroid(d)})`)
                                    .text(d => {
                                        // Only show labels for slices with a count greater than 0
                                        if (d.data.count > 0) {
                                            const percentage = (d.data.count / totalCount * 100).toFixed(1);
                                            return `${d.data.genre} (${percentage}%)`;
                                        }
                                        return ""; // Don't show label for 0 count slices
                                    })
                                    .style("text-anchor", d => {
                                        const midangle = d.startAngle + (d.endAngle - d.startAngle) / 2;
                                        return (midangle < Math.PI ? "start" : "end");
                                    })
                                    .attr("fill", "white")
                                    .style("font-size", "14px")
                                    .style("pointer-events", "none"); // Prevents text from blocking mouse events


                            } else {
                                genreDistributionContainer.append("p").text(`No genre data found for ${consoleName}.`).style("color", "white").style("text-align", "center");
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching genre distribution data:", error);
                            genreDistributionContainer.append("p").text(`Error loading genre distribution: ${error.message}`).style("color", "red").style("text-align", "center");
                        });
                }

                // --- ESRB Rating Distribution Pie Chart ---
                function loadESRBRatingChart(consoleName) {
                    const esrbRatingContainer = d3.select(".esrb-rating");
                    // Clear any previous chart content
                    esrbRatingContainer.selectAll("svg").remove();
                    esrbRatingContainer.selectAll("p").remove(); // Remove old messages

                    if (!consoleName) {
                        esrbRatingContainer.append("p").text("Select a console to see ESRB rating distribution.").style("color", "white").style("text-align", "center");
                        return;
                    }

                    // Append loading message
                    esrbRatingContainer.append("p").text("Loading ESRB rating distribution...").style("color", "white").style("text-align", "center");

                    const chartWidth = esrbRatingContainer.node().getBoundingClientRect().width;
                    const chartHeight = 300; // Use the min-height defined in CSS

                    const margin = { top: 0, right: 20, bottom: 20, left: 20 };
                    const width = chartWidth - margin.left - margin.right;
                    const height = chartHeight - margin.top - margin.bottom;
                    const radius = Math.min(width, height) / 2 - 20; // Radius for the pie chart

                    const svg = esrbRatingContainer.append("svg")
                        .attr("width", chartWidth)
                        .attr("height", chartHeight)
                        .append("g")
                        .attr("transform", `translate(${chartWidth / 2}, ${chartHeight / 2 + margin.top / 2})`);

                    const color = d3.scaleOrdinal(d3.schemeCategory10); // D3's built-in color scheme

                    const pie = d3.pie()
                        .value(d => d.count)
                        .sort(null); // No sorting, rely on API's sort or natural order

                    const arc = d3.arc()
                        .innerRadius(0)
                        .outerRadius(radius);

                    const outerArc = d3.arc()
                        .innerRadius(radius * 1.1)
                        .outerRadius(radius * 1.1);

                    const apiUrl = `../api/ConsoleView/get_esrb_rating.php?console_name=${encodeURIComponent(consoleName)}`;

                    fetch(apiUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            esrbRatingContainer.selectAll("p").remove(); // Remove loading message

                            if (data.success && data.data.length > 0) {
                                let esrbData = data.data;

                                const topN = 4;
                                let processedesrbData = [];
                                let otherCount = 0;

                                // Separate top N genres and sum the rest into 'Others'
                                if (esrbData.length > topN) {
                                    processedesrbData = esrbData.slice(0, topN); // Take the top N
                                    for (let i = topN; i < esrbData.length; i++) {
                                        otherCount += esrbData[i].count;
                                    }
                                    // Add 'Others' category if there are remaining genres
                                    if (otherCount > 0) {
                                        processedesrbData.push({ rating: "Others", count: otherCount });
                                    }
                                } else {
                                    // If less than or equal to N genres, show all of them
                                    processedesrbData = esrbData;
                                }

                                const totalCount = d3.sum(esrbData, d => d.count);
                                const arcs = pie(processedesrbData);

                                // Draw pie slices
                                svg.selectAll("slices")
                                    .data(arcs)
                                    .enter()
                                    .append("path")
                                    .attr("d", arc)
                                    .attr("fill", d => color(d.data.rating))
                                    .attr("stroke", "white")
                                    .style("stroke-width", "1px")
                                    .on("mouseover", function(event, d) {
                                        d3.select(this).transition()
                                            .duration(100)
                                            .attr("d", d3.arc().innerRadius(0).outerRadius(radius * 1.05)); // Slight grow effect
                                    })
                                    .on("mouseout", function(event, d) {
                                        d3.select(this).transition()
                                            .duration(100)
                                            .attr("d", arc); // Revert to original size
                                    });

                                // Add text labels
                                svg.selectAll("labels")
                                    .data(arcs)
                                    .enter()
                                    .append("text")
                                    .attr("transform", d => `translate(${outerArc.centroid(d)})`)
                                    .text(d => {
                                        if (d.data.count > 0) {
                                            const percentage = (d.data.count / totalCount * 100).toFixed(1);
                                            return `${d.data.rating} (${percentage}%)`;
                                        }
                                        return "";
                                    })
                                    .style("text-anchor", d => {
                                        const midangle = d.startAngle + (d.endAngle - d.startAngle) / 2;
                                        return (midangle < Math.PI ? "start" : "end");
                                    })
                                    .attr("fill", "white")
                                    .style("font-size", "14px")
                                    .style("pointer-events", "none"); // Prevents text from blocking mouse events

                            } else {
                                esrbRatingContainer.append("p").text(`No ESRB rating data found for ${consoleName}.`).style("color", "white").style("text-align", "center");
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching ESRB rating data:", error);
                            esrbRatingContainer.append("p").text(`Error loading ESRB ratings: ${error.message}`).style("color", "red").style("text-align", "center");
                        });
                }

                // --- Console Price Line Chart ---
                function loadConsolePriceChart(consoleName) {
                    const consolePriceContainer = d3.select(".console-price");
                    // Clear previous chart content
                    consolePriceContainer.html("");
                    consolePriceContainer.append("h4").text("Console Price"); // Re-add title

                    if (!consoleName) {
                        consolePriceContainer.append("p")
                            .text("Please select a console to view price history.")
                            .style("color", "white")
                            .style("text-align", "center");
                        return;
                    }

                    // Append loading message
                    consolePriceContainer.append("p")
                        .text("Loading price history...")
                        .style("color", "white")
                        .style("text-align", "center");

                    const margin = { top: 10, right: 30, bottom: 100, left: 100 };
                    const width = consolePriceContainer.node().getBoundingClientRect().width - margin.left - margin.right - 10;
                    const height = 500 - margin.top - margin.bottom; // Adjusted height for visibility

                    const svg = consolePriceContainer.append("svg")
                        .attr("width", width + margin.left + margin.right)
                        .attr("height", height + margin.top + margin.bottom)
                        .append("g")
                        .attr("transform", `translate(${margin.left},${margin.top})`);

                    const apiUrl = `../api/ConsoleView/get_console_price.php?console_name=${encodeURIComponent(consoleName)}`;

                    fetch(apiUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            consolePriceContainer.selectAll("p").remove(); // Remove loading message

                            if (data.success && data.data.length > 0) {
                                const priceData = data.data.map(d => ({
                                    date: new Date(d.Date), // Parse date strings into Date objects
                                    price: +d.Price // Convert price to number
                                })).sort((a, b) => a.date - b.date); // Ensure data is sorted by date

                                // X scale (Date)
                                const xScale = d3.scaleTime()
                                    .domain(d3.extent(priceData, d => d.date))
                                    .range([0, width]);

                                // Y scale (Price)
                                const yScale = d3.scaleLinear()
                                    .domain([0, d3.max(priceData, d => d.price) * 1.1]) // Add a small buffer to the max price
                                    .nice() // Extend domain to nice round values
                                    .range([height, 0]);

                                // Line generator
                                const line = d3.line()
                                    .x(d => xScale(d.date))
                                    .y(d => yScale(d.price));

                                // Add the X Axis
                                svg.append("g")
                                    .attr("class", "x-axis")
                                    .attr("transform", `translate(0,${height})`)
                                    .call(d3.axisBottom(xScale).tickFormat(d3.timeFormat("%Y-%m-%d")))
                                    .selectAll("text")
                                    .attr("transform", "rotate(-45)")
                                    .style("text-anchor", "end")
                                    .attr("fill", "white");

                                // Add the Y Axis
                                svg.append("g")
                                    .attr("class", "y-axis")
                                    .call(d3.axisLeft(yScale).tickFormat(d3.format("$,.0f"))) // Format as currency
                                    .selectAll("text")
                                    .attr("fill", "white");

                                // Add X Axis label
                                svg.append("text")
                                    .attr("transform", `translate(${width / 2},${height + margin.bottom - 10})`)
                                    .style("text-anchor", "middle")
                                    .attr("fill", "white")
                                    .style("font-size", "14px")
                                    .text("Date");

                                // Add Y Axis label
                                svg.append("text")
                                    .attr("transform", "rotate(-90)")
                                    .attr("y", 0 - margin.left + 15)
                                    .attr("x", 0 - (height / 2))
                                    .attr("dy", "1em")
                                    .style("text-anchor", "middle")
                                    .attr("fill", "white")
                                    .style("font-size", "14px")
                                    .text("Price ($)");

                                // Add the price line
                                svg.append("path")
                                    .datum(priceData)
                                    .attr("class", "line")
                                    .attr("d", line);

                                // Add dots for each data point and tooltip functionality
                                const priceTooltip = d3.select("body").append("div")
                                    .attr("class", "price-tooltip")
                                    .style("opacity", 0);

                                svg.selectAll(".dot")
                                    .data(priceData)
                                    .enter().append("circle")
                                    .attr("class", "dot")
                                    .attr("cx", d => xScale(d.date))
                                    .attr("cy", d => yScale(d.price))
                                    .attr("r", 4)
                                    .on("mouseover", function(event, d) {
                                        d3.select(this).transition().duration(50).attr("r", 6); // Enlarge dot on hover
                                        priceTooltip.transition()
                                            .duration(200)
                                            .style("opacity", .9);
                                        priceTooltip.html(`Date: ${d3.timeFormat("%Y-%m-%d")(d.date)}<br/>Price: $${d.price.toFixed(2)}`)
                                            .style("left", (event.pageX + 10) + "px")
                                            .style("top", (event.pageY - 28) + "px");
                                    })
                                    .on("mouseout", function(d) {
                                        d3.select(this).transition().duration(200).attr("r", 4); // Revert dot size
                                        priceTooltip.transition()
                                            .duration(500)
                                            .style("opacity", 0);
                                    });

                            } else {
                                consolePriceContainer.append("p")
                                    .text(`No price history found for ${consoleName}.`)
                                    .style("color", "white")
                                    .style("text-align", "center");
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching console price data:", error);
                            consolePriceContainer.append("p")
                                .text(`Error loading price chart: ${error.message}`)
                                .style("color", "red")
                                .style("text-align", "center");
                        });
                }


                // Event listener for dropdown change
                consoleSelect.addEventListener('change', function() {
                    const selectedConsole = this.value;
                    if (selectedConsole) {
                        window.location.href = `console.php?console_name=${encodeURIComponent(selectedConsole)}`;
                    } else {
                        window.location.href = `console.php`;
                    }
                });

                // On page load, check for console_name in URL and load details
                const initialConsoleName = getQueryParam('console_name');
                if (initialConsoleName) {
                    consoleSelect.value = initialConsoleName;
                    loadConsoleDetails(initialConsoleName); // This now triggers loadTopRatedGamesChart and loadGenreDistributionChart and loadESRBRatingChart
                } else {
                    loadConsoleDetails(null);
                    loadTopRatedGamesChart(null); // Also clear/default the games chart
                    loadGenreDistributionChart(null); // Clear/default the genre chart
                    loadESRBRatingChart(null); // Clear/default the ESRB chart
                    loadConsolePriceChart(null); // Clear/default the Console Price chart
                }
            });
            
        </script>
    </body>
</html>
<?php include "../resources/footer.php"; ?>
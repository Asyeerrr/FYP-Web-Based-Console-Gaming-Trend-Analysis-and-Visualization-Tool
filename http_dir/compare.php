<?php include "../resources/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Comparison View</title>
        <style>
            body {
                background-color: #000123;
                background-attachment: fixed;
                margin: 0;
                padding: 0;
                font-family: 'Inter', sans-serif;
                color: white;
            }

            .compare-container {
                min-width: 100vh;
                padding: 1em;
            }

            .compare-content {
                text-align: center;
                min-height: calc(100vh - 50px); /* Adjust for header/footer */
            }

            h1 {
                margin-bottom: 0px;
                font-size: 30px;
            }

            .selection-and-content-wrapper {
                display: flex;
                flex-direction: column; /* Stack header/dropdowns and grid vertically */
                gap: 1em;
                margin-top: 1em;
                background-color: #2d8bba3a; /* Background for the entire comparison block */
                border-radius: 10px;
                padding: 1em;
                box-sizing: border-box;
            }

            .console-selection-row {
                display: grid;
                grid-template-columns: repeat(3, 1fr); /* 3 equal columns for dropdowns */
                gap: 1em;
                padding-bottom: 1em; /* Space between selects and content */
            }

            .console-selection-row select {
                width: 100%; /* Fill the grid column */
                padding: 8px;
                border-radius: 5px;
                border: 1px solid #ccc;
                background-color: #5a7d9b;
                color: white;
                font-size: 16px;
            }

            /* Main comparison grid for content */
            .comparison-grid {
                display: grid;
                grid-template-columns: 0.8fr repeat(3, 1fr); /* Feature label column + 3 console columns */
                gap: 1em;
                align-items: stretch; /* Stretch items to fill height of their row */
            }

            .grid-label {
                background-color: #3d6c8c; /* Darker background for feature labels */
                padding: 10px;
                border-radius: 5px;
                text-align: left;
                font-weight: bold;
                display: flex; /* Use flex to vertically center content */
                align-items: center;
                justify-content: flex-start;
                min-height: 150px; /* Ensure labels have some height */
            }

            .grid-item {
                background-color: #4a6c8c5c; /* Background for content cells */
                padding: 10px;
                border-radius: 5px;
                min-height: 300px; /* Base min-height for all content cells */
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                box-sizing: border-box;
            }

            /* Specific styles for details cells */
            .console-details-cell {
                text-align: left;
                font-size: 14px;
                min-height: 250px; /* Increased height for details cell */
                justify-content: flex-start; /* Align content to top */
            }

            .console-details-cell img {
                max-width: 150px; /* Smaller images for comparison details */
                height: auto;
                margin-bottom: 5px;
                border-radius: 4px;
            }

            .console-details-cell p {
                margin: 2px 0;
            }

            /* Styles for D3 charts within grid items */
            .chart-svg {
                max-width: 100%;
                height: 100%; /* Fill parent height */
                display: block; /* Remove extra space below SVG */
            }
            .chart-container-cell {
                padding: 50px 10px 0px 10px;
                min-height: 300px; /* Ensure consistent height for charts */
                justify-content: center;
                align-items: center;
                position: relative; /* For tooltips */
            }

            /* Tooltip for D3 Charts */
            .tooltip {
                position: absolute;
                text-align: center;
                padding: 8px;
                font: 12px sans-serif;
                background: #333;
                color: white;
                border: 0px;
                border-radius: 8px;
                pointer-events: none;
                opacity: 0;
                z-index: 1000;
            }

            .chart-loading-message {
                color: #ccc;
                font-size: 1em;
                text-align: center;
            }

            /* Styles for top rated games list in comparison */
            .game-list-compare {
                list-style: none;
                padding: 0;
                text-align: left;
                font-size: 0.8em; /* Smaller font for game list */
                width: 100%; /* Ensure list takes full width of cell */
                max-height: 230px; /* Limit height and allow scroll */
                overflow-y: auto;
            }
            .game-list-compare li {
                margin-bottom: 5px;
                display: flex;
                align-items: flex-start; /* Align text to top of image */
            }
            .game-list-compare img {
                width: 25px; /* Even smaller game cover */
                height: 25px;
                object-fit: cover;
                margin-right: 5px;
                border-radius: 2px;
            }
            .game-list-compare .game-info-compare {
                flex-grow: 1;
            }
            .game-list-compare strong {
                display: block; /* Game name on its own line */
                font-size: 1.1em;
            }
            .game-list-compare span {
                font-size: 0.9em;
                color: #ddd;
            }

            /* D3 Chart Specifics */
            .x-axis text, .y-axis text {
                fill: white;
                font-size: 10px; /* Smaller font for axes labels in comparison */
            }
            .x-axis path, .y-axis path,
            .x-axis line, .y-axis line {
                stroke: white;
            }
            .line {
                fill: none;
                stroke: steelblue;
                stroke-width: 2px;
            }
            .dot {
                fill: steelblue;
                stroke: none; /* No outer circle */
                stroke-width: 0; /* No outer circle */
            }
            .arc-slice {
                /* General style for pie chart slices */
            }
            .arc-slice text {
                font-size: 9px; /* Smaller font for pie chart labels */
            }


            /* Media queries for responsiveness */
            @media (max-width: 992px) {
                .console-selection-row {
                    grid-template-columns: repeat(2, 1fr); /* 2 columns on medium screens */
                }
                .console-selection-row select:last-child { /* Move third select to next row */
                    grid-column: span 2; /* Span full width for third dropdown */
                }
                .comparison-grid {
                    grid-template-columns: 0.8fr repeat(2, 1fr); /* Feature label + 2 console columns */
                }
                .comparison-grid .grid-item:nth-child(4n) { /* Hide 3rd console column elements */
                    display: none;
                }
                /* Adjust grid-label height as needed */
                .grid-label {
                     min-height: 200px; /* Adjust height for feature labels */
                }
                .console-details-cell, .chart-container-cell {
                    min-height: 200px;
                }
            }

            @media (max-width: 768px) {
                .console-selection-row {
                    grid-template-columns: 1fr; /* Single column for dropdowns on small screens */
                }
                .console-selection-row select:last-child {
                    grid-column: auto; /* Revert span for third dropdown */
                }
                .comparison-grid {
                    grid-template-columns: 1fr; /* Single column for content, stacking */
                }
                .grid-label, .grid-item {
                    grid-column: 1 / -1; /* Make all items span full width */
                }
                .grid-label {
                    text-align: center;
                    font-size: 1.2em;
                    min-height: unset; /* Remove fixed height */
                    padding: 15px;
                }
                .grid-item {
                    min-height: unset; /* Remove fixed height */
                    margin-bottom: 1em; /* Space between stacked items */
                }
                .console-details-cell {
                    text-align: center; /* Center details when stacked */
                }
                .console-details-cell .text-details {
                    text-align: center; /* Center text within details */
                }
                .console-details-cell img {
                    margin: 0 auto 10px auto; /* Center image */
                }
                .game-list-compare {
                    text-align: center; /* Center game list content */
                }
                .game-list-compare li {
                    justify-content: center; /* Center list items */
                }
            }

        </style>
        <script src="https://d3js.org/d3.v7.min.js"></script>
    </head>
    <body class="compare-body">
        <div class="compare-container">
            <main class="compare-content">
                <h1>Compare Consoles</h1>
                <div class="selection-and-content-wrapper">
                    <div class="console-selection-row">
                        <select name="console-0" id="console-select-0">
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
                        <select name="console-1" id="console-select-1">
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
                        <select name="console-2" id="console-select-2">
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

                    <div class="comparison-grid">
                        <!-- Row 1: Console Details -->
                        <div class="grid-label">Console Details</div>
                        <div id="details-0" class="grid-item console-details-cell"></div>
                        <div id="details-1" class="grid-item console-details-cell"></div>
                        <div id="details-2" class="grid-item console-details-cell"></div>

                        <!-- Row 2: Top Rated Games -->
                        <div class="grid-label">Top Rated Games</div>
                        <div id="top-games-0" class="grid-item chart-container-cell"></div>
                        <div id="top-games-1" class="grid-item chart-container-cell"></div>
                        <div id="top-games-2" class="grid-item chart-container-cell"></div>

                        <!-- Row 3: Genre Distribution -->
                        <div class="grid-label">Genre Distribution</div>
                        <div id="genre-0" class="grid-item chart-container-cell"></div>
                        <div id="genre-1" class="grid-item chart-container-cell"></div>
                        <div id="genre-2" class="grid-item chart-container-cell"></div>

                        <!-- Row 4: ESRB Rating Distribution -->
                        <div class="grid-label">ESRB Rating Distribution</div>
                        <div id="esrb-0" class="grid-item chart-container-cell"></div>
                        <div id="esrb-1" class="grid-item chart-container-cell"></div>
                        <div id="esrb-2" class="grid-item chart-container-cell"></div>

                        <!-- Row 5: Console Price -->
                        <div class="grid-label">Console Price</div>
                        <div id="price-0" class="grid-item chart-container-cell"></div>
                        <div id="price-1" class="grid-item chart-container-cell"></div>
                        <div id="price-2" class="grid-item chart-container-cell"></div>
                    </div>
                </div>

            </main>
        </div>

        <script>
            // Utility function to get query parameter
            function getQueryParam(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // --- Console Details Renderer ---
            async function renderConsoleDetails(consoleName, targetCellId) {
                const targetCell = d3.select(`#${targetCellId}`);
                targetCell.html('<p class="chart-loading-message">Loading details...</p>');

                if (!consoleName) {
                    targetCell.html('<p class="chart-loading-message">Select a console</p>');
                    return;
                }

                try {
                    const response = await fetch(`../api/ConsoleView/get_console_details.php?console_name=${encodeURIComponent(consoleName)}`);
                    const data = await response.json();

                    if (data.success && data.data) {
                        const details = data.data;
                        targetCell.html(`
                            <img src="${details.ImageURL || 'https://placehold.co/80x80?text=Image+Not+Found'}" alt="${details.ConsoleName}" class="console-image">
                            <div class="text-details">
                                <p><strong>Name:</strong> ${details.ConsoleName}</p>
                                <p><strong>Publisher:</strong> ${details.Publisher}</p>
                                <p><strong>Released:</strong> ${details.Released}</p>
                                <p><strong>Discontinued:</strong> ${details.Discontinued}</p>
                                <p><strong>Original Price:</strong> $${parseFloat(details.OriginalPrice).toFixed(2)}</p>
                                <p><strong>Current Price:</strong> $${parseFloat(details.CurrentPrice).toFixed(2)}</p>
                                <p><strong>Generation:</strong> ${details.Generation}</p>
                            </div>
                        `);
                    } else {
                        targetCell.html(`<p class="chart-loading-message" style="color: red;">${data.message || 'No details found.'}</p>`);
                    }
                } catch (error) {
                    console.error(`Error fetching console details for ${consoleName}:`, error);
                    targetCell.html(`<p class="chart-loading-message" style="color: red;">Error: ${error.message}</p>`);
                }
            }

            // --- Top Rated Games List Renderer (adapted for table cell) ---
            async function renderTopRatedGamesChart(consoleName, targetCellId) {
                const targetCell = d3.select(`#${targetCellId}`);
                targetCell.html('<p class="chart-loading-message">Loading games...</p>');

                if (!consoleName) {
                    targetCell.html('<p class="chart-loading-message">Select a console</p>');
                    return;
                }

                try {
                    const response = await fetch(`../api/ConsoleView/get_rated_games.php?console_name=${encodeURIComponent(consoleName)}`);
                    const data = await response.json();

                    if (data.success && data.data.length > 0) {
                        const gamesData = data.data;
                        let gameListHtml = '<ul class="game-list-compare">';
                        gamesData.slice(0, 5).forEach(game => { // Limit to top 5 games
                            gameListHtml += `
                                <li>
                                    <img src="${game.ImageURL || 'https://placehold.co/30x30?text=IMG'}" alt="${game.Name}">
                                    <div class="game-info-compare">
                                        <strong>${game.Name}</strong>
                                        <span>Rating: ${parseFloat(game.Rating).toFixed(1)}</span>
                                    </div>
                                </li>
                            `;
                        });
                        gameListHtml += '</ul>';
                        targetCell.html(gameListHtml);
                    } else {
                        targetCell.html(`<p class="chart-loading-message">No top 5 rated games for ${consoleName}.</p>`);
                    }
                } catch (error) {
                    console.error(`Error fetching top rated games for ${consoleName}:`, error);
                    targetCell.html(`<p class="chart-loading-message" style="color: red;">Error: ${error.message}</p>`);
                }
            }


            // --- Genre Distribution Pie Chart Renderer ---
            async function renderGenreDistributionChart(consoleName, targetCellId) {
                const targetCell = d3.select(`#${targetCellId}`);
                targetCell.html('<p class="chart-loading-message">Loading genres...</p>'); // Initial loading message

                if (!consoleName) {
                    targetCell.html('<p class="chart-loading-message">Select a console</p>');
                    return;
                }

                try {
                    const response = await fetch(`../api/ConsoleView/get_genre_console.php?console_name=${encodeURIComponent(consoleName)}`);
                    const data = await response.json();

                    if (data.success && data.data.length > 0) {
                        let genreData = data.data;
                        
                        const topN = 5; // Display top 5 genres + Others
                        let processedGenreData = [];
                        let otherCount = 0;

                        if (genreData.length > topN) {
                            processedGenreData = genreData.slice(0, topN);
                            for (let i = topN; i < genreData.length; i++) {
                                otherCount += genreData[i].count;
                            }
                            if (otherCount > 0) {
                                processedGenreData.push({ genre: "Others", count: otherCount });
                            }
                        } else {
                            processedGenreData = genreData;
                        }

                        // Clear any previous chart content and loading message before drawing
                        targetCell.html(''); 

                        // Determine SVG size based on parent cell
                        // Use a fixed aspect ratio or direct pixel values if flexible sizing is problematic
                        const svgWidth = targetCell.node().getBoundingClientRect().width;
                        // Use a fixed height for pie charts to prevent vertical stretching
                        const svgHeight = 200; // Consistent height for pie charts in comparison
                        const radius = Math.min(svgWidth, svgHeight) / 2 - 10; 

                        const svg = targetCell.append("svg")
                            .attr("width", svgWidth)
                            .attr("height", svgHeight)
                            .attr("class", "chart-svg")
                            .append("g")
                            .attr("transform", `translate(${svgWidth / 2},${svgHeight / 2})`);

                        const color = d3.scaleOrdinal(d3.schemeCategory10);

                        const pie = d3.pie().value(d => d.count).sort(null);
                        const arc = d3.arc().innerRadius(0).outerRadius(radius);
                        const outerArc = d3.arc().innerRadius(radius * 1.1).outerRadius(radius * 1.1);

                        const arcs = svg.selectAll(".arc-slice")
                            .data(pie(processedGenreData))
                            .enter().append("g")
                            .attr("class", "arc-slice");

                        arcs.append("path")
                            .attr("d", arc)
                            .attr("fill", d => color(d.data.genre))
                            .attr("stroke", "white")
                            .style("stroke-width", "1px");

                        arcs.append("text")
                            .attr("transform", d => `translate(${outerArc.centroid(d)})`)
                            .attr("dy", "0.35em")
                            .text(d => {
                                const percentage = (d.data.count / d3.sum(processedGenreData, x => x.count) * 100).toFixed(0);
                                return `${d.data.genre} (${percentage}%)`;
                            })
                            .style("font-size", "10px") // Smaller font size for table cells
                            .attr("fill", "white")
                            .style("text-anchor", d => (d.endAngle + d.startAngle) / 2 > Math.PI ? "end" : "start")
                            .style("pointer-events", "none");

                    } else {
                        targetCell.html(`<p class="chart-loading-message">No genre data for ${consoleName}.</p>`);
                    }
                } catch (error) {
                    console.error(`Error fetching genre distribution for ${consoleName}:`, error);
                    targetCell.html(`<p class="chart-loading-message" style="color: red;">Error: ${error.message}</p>`);
                }
            }

            // --- ESRB Rating Distribution Pie Chart Renderer ---
            async function renderESRBRatingChart(consoleName, targetCellId) {
                const targetCell = d3.select(`#${targetCellId}`);
                targetCell.html('<p class="chart-loading-message">Loading ESRB data...</p>'); // Initial loading message

                if (!consoleName) {
                    targetCell.html('<p class="chart-loading-message">Select a console</p>');
                    return;
                }

                try {
                    const response = await fetch(`../api/ConsoleView/get_esrb_rating.php?console_name=${encodeURIComponent(consoleName)}`);
                    const data = await response.json();

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

                        // Clear any previous chart content and loading message before drawing
                        targetCell.html('');

                        const svgWidth = targetCell.node().getBoundingClientRect().width;
                        const svgHeight = 200; // Consistent height for pie charts
                        const radius = Math.min(svgWidth, svgHeight) / 2 - 10;

                        const svg = targetCell.append("svg")
                            .attr("width", svgWidth)
                            .attr("height", svgHeight)
                            .attr("class", "chart-svg")
                            .append("g")
                            .attr("transform", `translate(${(svgWidth / 2) + 10},${(svgHeight / 2) + 10})`);

                        const color = d3.scaleOrdinal(d3.schemeCategory10);

                        const pie = d3.pie().value(d => d.count).sort(null);
                        const arc = d3.arc().innerRadius(0).outerRadius(radius);
                        const outerArc = d3.arc().innerRadius(radius * 1.1).outerRadius(radius * 1.1);

                        const arcs = svg.selectAll(".arc-slice")
                            .data(pie(processedesrbData))
                            .enter().append("g")
                            .attr("class", "arc-slice");

                        arcs.append("path")
                            .attr("d", arc)
                            .attr("fill", d => color(d.data.rating))
                            .attr("stroke", "white")
                            .style("stroke-width", "1px");

                        arcs.append("text")
                            .attr("transform", d => `translate(${outerArc.centroid(d)})`)
                            .attr("dy", "0.35em")
                            .text(d => {
                                const percentage = (d.data.count / d3.sum(processedesrbData, x => x.count) * 100).toFixed(0);
                                return `${d.data.rating} (${percentage}%)`;
                            })
                            .style("font-size", "10px") // Smaller font size for table cells
                            .attr("fill", "white")
                            .style("text-anchor", d => (d.endAngle + d.startAngle) / 2 > Math.PI ? "end" : "start")
                            .style("pointer-events", "none");

                    } else {
                        targetCell.html(`<p class="chart-loading-message">No ESRB data for ${consoleName}.</p>`);
                    }
                } catch (error) {
                    console.error(`Error fetching ESRB data for ${consoleName}:`, error);
                    targetCell.html(`<p class="chart-loading-message" style="color: red;">Error: ${error.message}</p>`);
                }
            }

            // --- Console Price Line Chart Renderer ---
            async function renderConsolePriceChart(consoleName, targetCellId) {
                const targetCell = d3.select(`#${targetCellId}`);
                targetCell.html('<p class="chart-loading-message">Loading price data...</p>'); // Initial loading message

                if (!consoleName) {
                    targetCell.html('<p class="chart-loading-message">Select a console</p>');
                    return;
                }

                try {
                    const response = await fetch(`../api/ConsoleView/get_console_price.php?console_name=${encodeURIComponent(consoleName)}`);
                    const data = await response.json();

                    if (data.success && data.data.length > 0) {
                        const priceData = data.data.map(d => ({
                            date: new Date(d.Date),
                            price: +d.Price
                        })).filter(d => d.price > 0).sort((a, b) => a.date - b.date); // Filter and sort

                        if (priceData.length === 0) {
                            targetCell.html(`<p class="chart-loading-message">No valid price history (price > 0) for ${consoleName}.</p>`);
                            return;
                        }

                        // Clear any previous chart content and loading message before drawing
                        targetCell.html('');

                        // Determine SVG size based on parent cell
                        const svgWidth = targetCell.node().getBoundingClientRect().width;
                        const svgHeight = 200; // Consistent height for line charts
                        const margin = { top: 20, right: 30, bottom: 0, left: 50 }; // Smaller margins for table cell
                        const width = svgWidth - margin.left - margin.right;
                        const height = svgHeight - margin.top - margin.bottom;

                        const svg = targetCell.append("svg")
                            .attr("width", svgWidth)
                            .attr("height", svgHeight)
                            .attr("class", "chart-svg")
                            .append("g")
                            .attr("transform", `translate(${margin.left},${margin.top})`);

                        const xScale = d3.scaleTime()
                            .domain(d3.extent(priceData, d => d.date))
                            .range([0, width]);

                        const yScale = d3.scaleLinear()
                            .domain([0, d3.max(priceData, d => d.price) * 1.1]).nice()
                            .range([height, 0]);

                        const line = d3.line()
                            .x(d => xScale(d.date))
                            .y(d => yScale(d.price));

                        svg.append("path")
                            .datum(priceData)
                            .attr("fill", "none")
                            .attr("stroke", "#69b3a2")
                            .attr("stroke-width", 2)
                            .attr("d", line);

                        // Tooltip for line chart
                        const priceTooltip = d3.select("body").append("div")
                            .attr("class", "tooltip")
                            .style("opacity", 0);

                        svg.selectAll(".dot")
                            .data(priceData)
                            .enter().append("circle")
                            .attr("class", "dot")
                            .attr("cx", d => xScale(d.date))
                            .attr("cy", d => yScale(d.price))
                            .attr("r", 0) // Smaller dot radius for comparison
                            .attr("fill", "#69b3a2")
                            .attr("stroke", "none")
                            .attr("stroke-width", 0)
                            .on("mouseover", function(event, d) {
                                d3.select(this).transition().duration(50).attr("r", 5);
                                priceTooltip.transition()
                                    .duration(200)
                                    .style("opacity", .9);
                                priceTooltip.html(`Date: ${d3.timeFormat("%Y-%m-%d")(d.date)}<br/>Price: $${d.price.toFixed(2)}`)
                                    .style("left", (event.pageX + 10) + "px")
                                    .style("top", (event.pageY - 28) + "px");
                            })
                            .on("mouseout", function(d) {
                                d3.select(this).transition().duration(200).attr("r", 3);
                                priceTooltip.transition()
                                    .duration(500)
                                    .style("opacity", 0);
                            });

                        // X-axis
                        svg.append("g")
                            .attr("transform", `translate(0,${height})`)
                            .call(d3.axisBottom(xScale).ticks(d3.timeYear.every(2)).tickFormat(d3.timeFormat("%Y"))) // Show year every 2 years
                            .selectAll("text")
                            .attr("fill", "white")
                            .attr("transform", "rotate(-45)")
                            .style("text-anchor", "end")
                            .style("font-size", "9px"); // Smaller font size

                        // Y-axis
                        svg.append("g")
                            .call(d3.axisLeft(yScale).ticks(5).tickFormat(d3.format("$.0f"))) // Fewer ticks, currency format
                            .selectAll("text")
                            .attr("fill", "white")
                            .style("font-size", "9px"); // Smaller font size


                    } else {
                        targetCell.html(`<p class="chart-loading-message">No price history data for ${consoleName}.</p>`);
                    }
                } catch (error) {
                    console.error(`Error fetching price data for ${consoleName}:`, error);
                    targetCell.html(`<p class="chart-loading-message" style="color: red;">Error: ${error.message}</p>`);
                }
            }


            // --- Main Logic for Comparison Page ---
            document.addEventListener('DOMContentLoaded', function() {
                const consoleSelects = [
                    document.getElementById('console-select-0'),
                    document.getElementById('console-select-1'),
                    document.getElementById('console-select-2')
                ];

                // Centralized tooltip for all D3 charts in comparison view
                const tooltip = d3.select("body").append("div")
                    .attr("class", "tooltip")
                    .style("opacity", 0);

                const renderChartsForConsole = (consoleName, index) => {
                    renderConsoleDetails(consoleName, `details-${index}`);
                    renderTopRatedGamesChart(consoleName, `top-games-${index}`);
                    renderGenreDistributionChart(consoleName, `genre-${index}`);
                    renderESRBRatingChart(consoleName, `esrb-${index}`);
                    renderConsolePriceChart(consoleName, `price-${index}`);
                };

                // Initialize charts based on URL parameters or default empty state
                const initialConsoleNames = [];
                for (let i = 0; i < consoleSelects.length; i++) {
                    const paramName = `console_${i}`;
                    const initialName = getQueryParam(paramName);
                    if (initialName) {
                        // Set dropdown value (decodeURIComponent is important here)
                        consoleSelects[i].value = decodeURIComponent(initialName);
                        initialConsoleNames[i] = decodeURIComponent(initialName);
                    } else {
                        initialConsoleNames[i] = ""; // Keep empty if no param
                    }
                    renderChartsForConsole(initialConsoleNames[i], i); // Render for each initial console
                }

                // Event Listeners for Dropdowns
                consoleSelects.forEach((select, index) => {
                    select.addEventListener('change', function() {
                        const selectedConsole = this.value;
                        const currentURL = new URL(window.location.href);
                        const paramName = `console_${index}`;

                        if (selectedConsole) {
                            currentURL.searchParams.set(paramName, encodeURIComponent(selectedConsole));
                        } else {
                            currentURL.searchParams.delete(paramName);
                        }
                        // Reload the page with updated parameters
                        window.location.href = currentURL.toString();
                    });
                });

                // Optional: Adjust chart sizes on window resize for better responsiveness
                window.addEventListener('resize', () => {
                    initialConsoleNames.forEach((consoleName, index) => {
                        if (consoleName) { // Only re-render if a console is actually selected
                            renderConsoleDetails(consoleName, `details-${index}`); // Re-render details to adjust image size
                            renderTopRatedGamesChart(consoleName, `top-games-${index}`);
                            renderGenreDistributionChart(consoleName, `genre-${index}`);
                            renderESRBRatingChart(consoleName, `esrb-${index}`);
                            renderConsolePriceChart(consoleName, `price-${index}`);
                        }
                    });
                });
            });
        </script>
    </body>
</html>
<?php include "../resources/footer.php"; ?>
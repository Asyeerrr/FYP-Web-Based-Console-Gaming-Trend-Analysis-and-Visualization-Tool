<?php include "../resources/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Console Market View</title>
        <style>
            .market-body {
                background-color: #000123;
                background-attachment: fixed;
                margin: 0;
                padding: 0;
                font-family: 'Inter', sans-serif; /* Ensure a consistent font */
            }

            .market-container {
                min-width: 100vh;
            }

            .market-content {
                min-height: calc(100vh - 50px);
                color: white;
                text-align: center;
                font-size: 30px;
            }

            .market-content h1 {
                margin-bottom: 0px; 
            }

            .market-visual {
                display: flex; /* Changed to flex for row layout */
                flex-wrap: wrap; /* Allow items to wrap to the next line */
                justify-content: space-between;
                gap: 1em;
                padding: 1em;
                padding-top: 0px;
            }

            .market-left {
                display: flex;
                flex-direction: column;
                flex: 2; /* Make the left side (with gen-console) wider */
                min-width: 60%; /* Ensure it takes a good portion of the width */
                gap: 0.3em;
            }

            .market-right {
                display: flex;
                flex-direction: column;
                flex: 1; /* Make the right side (with top-genre) narrower */
                min-width: 35%; /* Ensure it takes a good portion of the width */
                gap: 0.5em;
            }

            /* Responsive adjustments for smaller screens */
            @media (max-width: 1024px) {
                .market-left,
                .market-right {
                    flex: 1 1 100%; /* Stack columns on top of each other */
                    min-width: unset; /* Remove min-width to allow full width */
                }
            }


            .console-market-share,
            .gen-console,
            .top-selling-console,
            .top-genre {
                text-align: center;
                color: white;
                background-color: #2d8bba3a;
                padding: 20px;
                border-radius: 10px;
                font-size: 20px;
                width: 100%; /* Ensure they take full width of their flex parent */
                box-sizing: border-box; /* Include padding in width */
            }

            .console-market-share {
                min-height: 300px;
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .console-market-share h4 {
                position: absolute;
                top: 10px;
                width: 100%;
                text-align: center;
                margin: 0;
                color: white;
            }

            .top-selling-console {
                min-height: 350px;
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
                margin-bottom: 0px;
            }

            .top-selling-console h4 {
                position: absolute;
                top: 10px;
                width: 100%;
                text-align: center;
                margin: 0;
                color: white;
            }

            .podium-bar {
                fill: steelblue;
                transition: fill 0.3s ease;
            }

            .podium-bar.first { fill: gold; }
            .podium-bar.second { fill: silver; }
            .podium-bar.third { fill: #cd7f32; }

            .podium-bar-label {
                fill: white;
                font-size: 14px;
                text-anchor: middle;
            }

            /* Styles for the Generation Console chart */
            .gen-console {
                min-height: 500px;
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .gen-console h4 {
                position: absolute;
                top: 10px;
                width: 100%;
                text-align: center;
                margin: 0;
                color: white;
            }

            /* Styles for D3.js axes */
            .x-axis text, .y-axis text {
                fill: white;
            }
            .x-axis path, .y-axis path,
            .x-axis line, .y-axis line {
                stroke: white;
            }

            /* Tooltip for console details on hover */
            .tooltip {
                position: absolute;
                background-color: #333;
                color: white;
                padding: 8px;
                border-radius: 5px;
                pointer-events: none; /* Allows mouse events to pass through to the element below */
                opacity: 0;
                font-size: 12px;
                box-shadow: 0 0 10px rgba(0,0,0,0.5);
                z-index: 1000;
                display: flex;
                flex-direction: column;
                align-items: center;
                white-space: nowrap; /* Prevent text wrapping */
            }
            .tooltip img {
                margin-bottom: 5px;
                width: 75px;
                height: 75px;
                object-fit: contain; /* Ensure image scales properly within its bounds */
            }

            /* Styles for Top Genre Bar Chart */
            .top-genre {
                min-height: 540px; /* Ensure enough height for the bar chart */
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .top-genre h4 {
                position: absolute;
                top: 10px;
                width: 100%;
                text-align: center;
                margin: 0;
                color: white;
            }
            .bar {
                fill: steelblue; /* Default bar color */
            }
            .bar-label {
                fill: white;
                font-size: 12px;
                text-anchor: middle; /* Align text to the middle of the bar for vertical */
            }

         </style>
         <script src="https://d3js.org/d3.v7.min.js"></script>
    </head>
    <body class="market-body">
        <div class="market-container">
            
            <main class="market-content">

                <h1>Console Market</h1>
                <br>
                <div class="market-visual">

                    <div class="market-left">
                        <div class="console-market-share">
                            <h4>Console Market Share</h4>
                        </div>

                        <div class="gen-console">
                            <h4>Generation Console</h4>
                        </div>
                    </div>
                        
                    <div class="market-right">
                        <div class="top-selling-console" style="margin:0px 0px 0px 0px;">
                            <h4>Top Selling Console</h4>
                        </div>

                        <div class="top-genre">
                            <h4>Top Genre</h4>
                        </div>
                    </div>
                    
                </div>
                    
            </main>
            
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // --- Console Market Share Pie Chart ---
                const marketShareContainer = d3.select(".console-market-share");
                const marketShareContainerWidth = marketShareContainer.node().getBoundingClientRect().width;
                const marketShareContainerHeight = 350; // Keep a fixed height for consistency

                const marketShareMargin = { top: 40, right: 20, bottom: 20, left: 20 };
                const marketShareWidth = marketShareContainerWidth - marketShareMargin.left - marketShareMargin.right;
                const marketShareHeight = marketShareContainerHeight - marketShareMargin.top - marketShareMargin.bottom;
                const marketShareRadius = Math.min(marketShareWidth, marketShareHeight) / 2 - 30; // Reduced radius to make space for labels

                const marketShareSvg = marketShareContainer.append("svg")
                    .attr("width", marketShareContainerWidth)
                    .attr("height", marketShareContainerHeight)
                    .append("g")
                    .attr("transform", `translate(${marketShareContainerWidth / 2}, ${marketShareContainerHeight / 2 + marketShareMargin.top / 2})`);

                const color = d3.scaleOrdinal()
                    .domain(["PlayStation", "Xbox", "Nintendo"])
                    .range(["#0072CE", "#107C10", "#E4000F"]);

                // Set up pie generator
                const pie = d3.pie()
                    .value(d => d.TotalSales)
                    .sort(null);

                // Set up arc generator for drawing slices
                const arc = d3.arc()
                    .innerRadius(0)
                    .outerRadius(marketShareRadius); // Changed 'radius' to 'marketShareRadius'

                // Set up arc generator for positioning outer labels
                const outerArc = d3.arc()
                    .innerRadius(marketShareRadius * 1.1) // Slightly outside the pie
                    .outerRadius(marketShareRadius * 1.1);

                // Fetch data from API
                fetch('../api/ConsoleMarketView/get_console_market.php') // Ensure this path is correct
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            const marketData = data.data;
                            const totalSales = d3.sum(marketData, d => d.TotalSales);
                            const arcs = pie(marketData);

                            // Draw pie slices
                            marketShareSvg.selectAll("allSlices") // Changed 'svg' to 'marketShareSvg'
                                .data(arcs)
                                .enter()
                                .append("path")
                                .attr("d", arc)
                                .attr("fill", d => color(d.data.Platform))
                                .on("mouseover", function(event, d) {
                                    d3.select(this).transition()
                                        .duration(100)
                                        .attr("d", d3.arc().innerRadius(0).outerRadius(marketShareRadius * 1.05)); // Slight grow effect
                                })
                                .on("mouseout", function(event, d) {
                                    d3.select(this).transition()
                                        .duration(100)
                                        .attr("d", arc); // Revert to original size
                                });

                            // Add text labels
                            marketShareSvg.selectAll("allLabels") // Changed 'svg' to 'marketShareSvg'
                                .data(arcs)
                                .enter()
                                .append("text")
                                .text(d => {
                                    const percentage = (d.data.TotalSales / totalSales * 100).toFixed(1);
                                    return `${d.data.Platform} (${percentage}%)`;
                                })
                                .attr("transform", function(d) {
                                    const pos = outerArc.centroid(d);
                                    const midangle = d.startAngle + (d.endAngle - d.startAngle) / 2; // Fix here
                                    pos[0] = marketShareRadius * 1.05 * (midangle < Math.PI ? 1 : -1); // Adjust X to move labels farther or closer
                                    pos[1] *= 1; // Optional: Adjust Y to space out vertically
                                    return `translate(${pos})`;
                                })
                                .style("text-anchor", function(d) {
                                    const midangle = d.startAngle + (d.endAngle - d.startAngle) / 2;
                                    return (midangle < Math.PI ? "start" : "end"); // Align text based on side of chart
                                })
                                .style("font-size", "16px")
                                .attr("fill", "white")
                                .style("pointer-events", "none"); // Prevents text from blocking mouse events

                        } else {
                            console.error("Failed to fetch console market data or no data found:", data.message);
                            marketShareContainer.append("p").text("No market data available to display.").style("color", "white").style("text-align", "center");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching console market data:", error);
                        marketShareContainer.append("p").text(`Error loading chart data: ${error.message}`).style("color", "red").style("text-align", "center");
                    });

                // --- Top Selling Console Podium Visual ---
                const podiumContainer = d3.select(".top-selling-console");
                // Adjusted width calculation to use the full container width for better centering
                const podiumContainerWidth = podiumContainer.node().getBoundingClientRect().width - 50; 
                const podiumContainerHeight = 350;

                const podiumMargin = { top: 40, right: 20, bottom: 20, left: 20 };
                const podiumWidth = podiumContainerWidth - podiumMargin.left - podiumMargin.right;
                const podiumHeight = podiumContainerHeight - podiumMargin.top - podiumMargin.bottom;

                const podiumSvg = podiumContainer.append("svg")
                    .attr("width", podiumContainerWidth)
                    .attr("height", podiumContainerHeight)
                    .append("g")
                    .attr("transform", `translate(${podiumMargin.left}, ${podiumMargin.top})`);

                fetch('../api/ConsoleMarketView/get_console_sales.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            const top3Consoles = data.data.slice(0, 3);

                            // Define fixed heights for 1st, 2nd, 3rd place podium steps
                            const height1st = podiumHeight * 0.6; // Tallest
                            const height2nd = podiumHeight * 0.4; // Medium
                            const height3rd = podiumHeight * 0.2; // Shortest

                            // Heights array in podium display order (2nd, 1st, 3rd)
                            const podiumHeights = [height2nd, height1st, height3rd];

                            const barWidth = 150;
                            const totalBarWidth = barWidth * 3;
                            // Recalculate bar spacing based on actual podiumWidth
                            const barSpacing = (podiumWidth - totalBarWidth) / 4; 

                            // X positions for 2nd, 1st, 3rd place
                            const xPositions = [
                                barSpacing + barWidth / 2,                  // 2nd place (left)
                                barSpacing * 2 + barWidth / 2 + barWidth,    // 1st place (center)
                                barSpacing * 3 + barWidth / 2 + barWidth * 2 // 3rd place (right)
                            ];

                            // Data for 2nd, 1st, 3rd place
                            const podiumOrder = [top3Consoles[1], top3Consoles[0], top3Consoles[2]]; 

                            // Draw podium bars
                            podiumSvg.selectAll(".podium-bar")
                                .data(podiumOrder)
                                .enter()
                                .append("rect")
                                .attr("class", (d, i) => {
                                    let cls = "podium-bar";
                                    if (i === 1) cls += " first"; // 1st place (index 1 in podiumOrder)
                                    else if (i === 0) cls += " second"; // 2nd place (index 0)
                                    else if (i === 2) cls += " third"; // 3rd place (index 2)
                                    return cls;
                                })
                                .attr("x", (d, i) => xPositions[i] - barWidth / 2) // Center bar at xPosition
                                .attr("y", (d, i) => podiumHeight - podiumHeights[i]) // Calculate y based on fixed height
                                .attr("width", barWidth)
                                .attr("height", (d, i) => podiumHeights[i]) // Use fixed height
                                .attr("rx", 5)
                                .attr("ry", 5);

                            // Add console images
                            podiumSvg.selectAll(".podium-image")
                                .data(podiumOrder)
                                .enter()
                                .append("image")
                                .attr("class", "podium-image")
                                .attr("xlink:href", d => d.ImageURL) // Use ImageURL from your data
                                .attr("x", (d, i) => xPositions[i] - 50) // Center image (adjust 25 based on image width/2)
                                .attr("y", (d, i) => podiumHeight - podiumHeights[i] - 130) // Position image above text labels
                                .attr("width", 100) // Adjust image width as needed
                                .attr("height", 100); // Adjust image height as needed

                            // Add labels for console name and sales
                            podiumSvg.selectAll(".podium-name-label")
                                .data(podiumOrder)
                                .enter()
                                .append("text")
                                .attr("class", "podium-bar-label")
                                .attr("x", (d, i) => xPositions[i])
                                .attr("y", (d, i) => podiumHeight - podiumHeights[i] - 20) // Position below image, above sales
                                .text(d => d.Console)
                                .style("font-size", "14px")
                                .style("font-weight", "bold");

                            podiumSvg.selectAll(".podium-sales-label")
                                .data(podiumOrder)
                                .enter()
                                .append("text")
                                .attr("class", "podium-bar-label")
                                .attr("x", (d, i) => xPositions[i])
                                .attr("y", (d, i) => podiumHeight - podiumHeights[i] - 5) // Position below console name
                                .text(d => `${parseFloat(d.TotalSales).toFixed(2)}M`)
                                .style("font-size", "12px");

                            // Add rank labels (1st, 2nd, 3rd) below the bars
                            podiumSvg.selectAll(".podium-rank-label")
                                .data(podiumOrder)
                                .enter()
                                .append("text")
                                .attr("class", "podium-bar-label")
                                .attr("x", (d, i) => xPositions[i])
                                .attr("y", podiumHeight - 20) // Below the bars
                                .text((d,i) => {
                                    if (i === 1) return "1st"; // The actual 1st place data is at index 1 in podiumOrder
                                    if (i === 0) return "2nd"; // The actual 2nd place data is at index 0
                                    if (i === 2) return "3rd"; // The actual 3rd place data is at index 2
                                    return "";
                                })
                                .style("font-weight", "bold")
                                .style("font-size", "30px");

                        } else {
                            console.error("Failed to fetch top selling console data or no data found:", data.message);
                            podiumContainer.append("p").text("No top selling console data available.").style("color", "white").style("text-align", "center");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching top selling console data:", error);
                        podiumContainer.append("p").text(`Error loading top selling console data: ${error.message}`).style("color", "red").style("text-align", "center");
                    });

                // --- Generation Console Timeline Chart ---
                const genConsoleContainer = d3.select(".gen-console");
                const genContainerWidth = genConsoleContainer.node().getBoundingClientRect().width - 50;
                const genContainerHeight = 500; 

                const genMargin = { top: 60, right: 30, bottom: 60, left: 80 }; 
                const genWidth = genContainerWidth - genMargin.left - genMargin.right; 
                const genHeight = genContainerHeight - genMargin.top - genMargin.bottom; 

                const genSvg = genConsoleContainer.append("svg")
                    .attr("width", genContainerWidth) 
                    .attr("height", genContainerHeight) 
                    .append("g") 
                    .attr("transform", `translate(${genMargin.left}, ${genMargin.top})`); 

                // Tooltip for console details on hover (Defined once for this chart)
                const genTooltip = d3.select("body").append("div") // Use a unique name to avoid conflict
                    .attr("class", "tooltip")
                    .style("opacity", 0); // Hidden by default

                fetch('../api/ConsoleMarketView/get_console_gen.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            const genData = data.data;

                            // Parse Released year to Date objects
                            genData.forEach(d => {
                                d.Released = new Date(d.Released, 0, 1); // Set to January 1st of the released year
                            });

                            // Get unique generations for Y-axis
                            const generations = [...new Set(genData.map(d => d.Generation))].sort((a, b) => a - b);

                            // Scale for coloring generations
                            const generationColorScale = d3.scaleOrdinal(d3.schemeCategory10)
                                .domain(generations);

                            // Scales
                            const xScale = d3.scaleTime()
                                .domain(d3.extent(genData, d => d.Released))
                                .range([0, genWidth]);

                            const yScale = d3.scalePoint()
                                .domain(generations)
                                .range([0, genHeight])
                                .padding(0.5); // Add padding between points

                            // Group data by Generation and Released year to identify overlaps
                            const groupedData = d3.group(genData, d => d.Generation, d => d.Released.getFullYear());

                            // Apply offset to overlapping points
                            genData.forEach(d => {
                                const yearGroup = groupedData.get(d.Generation)?.get(d.Released.getFullYear());
                                if (yearGroup && yearGroup.length > 1) {
                                    const indexInGroup = yearGroup.indexOf(d);
                                    d.offsetY = (indexInGroup - (yearGroup.length - 1) / 2) * (yScale.step() * 0.15); // Increased multiplier for more separation
                                } else {
                                    d.offsetY = 0;
                                }
                            });


                            // X-axis
                            genSvg.append("g")
                                .attr("class", "x-axis")
                                .attr("transform", `translate(0, ${genHeight})`)
                                .call(d3.axisBottom(xScale).tickFormat(d3.timeFormat("%Y")))
                                .selectAll("text")
                                .attr("fill", "white")
                                .style("font-size", "16px");

                            // Y-axis (Generations)
                            genSvg.append("g")
                                .attr("class", "y-axis")
                                .call(d3.axisLeft(yScale))
                                .selectAll("text")
                                .attr("fill", "white")
                                .style("font-size", "16px");

                            // Add text label for Y-axis
                            genSvg.append("text")
                                .attr("transform", "rotate(-90)")
                                .attr("y", 0 - genMargin.left + 20)
                                .attr("x", 0 - (genHeight / 2))
                                .attr("dy", "1em")
                                .style("text-anchor", "middle")
                                .style("fill", "white")
                                .style("font-size", "20px")
                                .text("Generation");

                            // Draw horizontal lines (tracks) for each generation
                            genSvg.selectAll(".gen-track")
                                .data(generations)
                                .enter()
                                .append("line")
                                .attr("class", "gen-track")
                                .attr("x1", 0)
                                .attr("x2", genWidth)
                                .attr("y1", d => yScale(d))
                                .attr("y2", d => yScale(d))
                                .attr("stroke", "#ffffff33")
                                .attr("stroke-dasharray", "2,2");

                            // Add console points (circles only, image and name in tooltip)
                            const consoleGroups = genSvg.selectAll(".console-group")
                                .data(genData)
                                .enter()
                                .append("g")
                                .attr("class", "console-group")
                                .attr("transform", d => `translate(${xScale(d.Released)}, ${yScale(d.Generation) + d.offsetY})`); // Apply offsetY here

                            // Add circles (points)
                            consoleGroups.append("circle")
                                .attr("r", 8)
                                .attr("fill", d => generationColorScale(d.Generation))
                                .attr("stroke", "white")
                                .attr("stroke-width", 2);

                            // Re-attach tooltip events correctly to the 'g' element
                            consoleGroups
                                .on("mouseover", function(event, d) {
                                    genTooltip.transition() // Use genTooltip here
                                        .duration(200)
                                        .style("opacity", .9);
                                    genTooltip.html(`
                                        <img src="${d.ImageURL}" alt="${d.Console}" style="width:100px; height:100px;"/>
                                        <strong style="font-size:20px;">${d.Console}</strong><br>
                                        <p style="font-size:15px; margin: 0px 0px 0px 0px;">Generation: ${d.Generation}</p><br>
                                        <p style="font-size:15px; margin: 0px 0px 0px 0px;">Released: ${d.Released.getFullYear()}</p>
                                    `)
                                    .style("left", (event.pageX) + "px")
                                    .style("top", (event.pageY - 200) + "px");
                                })
                                .on("mouseout", function(d) {
                                    genTooltip.transition() // Use genTooltip here
                                        .duration(500)
                                        .style("opacity", 0);
                                });


                        } else {
                            console.error("Failed to fetch console generation data or no data found:", data.message);
                            genConsoleContainer.append("p").text("No generation data available to display.").style("color", "white").style("text-align", "center");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching console generation data:", error);
                        genConsoleContainer.append("p").text(`Error loading chart data: ${error.message}`).style("color", "red").style("text-align", "center");
                    });

                // --- Top Genre Bar Chart ---
                const topGenreContainer = d3.select(".top-genre");
                const topGenreContainerWidth = topGenreContainer.node().getBoundingClientRect().width - 50;
                const topGenreContainerHeight = 450; // Keep height consistent with podium/pie

                const topGenreMargin = { top: 40, right: 20, bottom: 40, left: 80 }; // Adjusted left margin for labels
                const topGenreWidth = topGenreContainerWidth - topGenreMargin.left - topGenreMargin.right;
                const topGenreHeight = topGenreContainerHeight - topGenreMargin.top - topGenreMargin.bottom;

                const topGenreSvg = topGenreContainer.append("svg")
                    .attr("width", topGenreContainerWidth)
                    .attr("height", topGenreContainerHeight)
                    .append("g")
                    .attr("transform", `translate(${topGenreMargin.left}, ${topGenreMargin.top})`);

                fetch('../api/ConsoleMarketView/get_genre_market.php') // Ensure this path is correct
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            // Sort data by count (already sorted in API, but good to re-confirm or limit)
                            const genreData = data.data.slice(0, 10); // Take top 10 genres

                            // X scale for genre names (vertical bar chart)
                            const xScaleGenre = d3.scaleBand()
                                .domain(genreData.map(d => d.genre))
                                .range([0, topGenreWidth])
                                .padding(0.1);

                            // Y scale for counts
                            const yScaleGenre = d3.scaleLinear()
                                .domain([0, d3.max(genreData, d => d.count)])
                                .range([topGenreHeight, 0]); // Inverted range for vertical bars

                            // X-axis (genre names)
                            topGenreSvg.append("g")
                                .attr("class", "x-axis")
                                .attr("transform", `translate(0, ${topGenreHeight})`)
                                .call(d3.axisBottom(xScaleGenre))
                                .selectAll("text")
                                .attr("fill", "white")
                                .attr("transform", "rotate(-45)") // Rotate labels for better readability
                                .style("text-anchor", "end"); // Anchor to the end of the rotated text

                            // Y-axis (counts)
                            topGenreSvg.append("g")
                                .attr("class", "y-axis")
                                .call(d3.axisLeft(yScaleGenre))
                                .selectAll("text")
                                .attr("fill", "white");
                            
                            // Add text label for Y-axis
                            topGenreSvg.append("text")
                                .attr("transform", "rotate(-90)")
                                .attr("y", 0 - topGenreMargin.left + 15) // Adjusted position
                                .attr("x", 0 - (topGenreHeight / 2))
                                .attr("dy", "1em")
                                .style("text-anchor", "middle")
                                .style("fill", "white")
                                .style("font-size", "16px")
                                .text("Number of Games");

                            // Draw bars
                            topGenreSvg.selectAll(".bar")
                                .data(genreData)
                                .enter()
                                .append("rect")
                                .attr("class", "bar")
                                .attr("x", d => xScaleGenre(d.genre)) // X position by genre band
                                .attr("y", d => yScaleGenre(d.count)) // Y position by count (top of the bar)
                                .attr("width", xScaleGenre.bandwidth()) // Width of the bar
                                .attr("height", d => topGenreHeight - yScaleGenre(d.count)) // Height of the bar
                                .attr("fill", "#69b3a2") // A pleasant bar color
                                .on("mouseover", function() {
                                    d3.select(this).attr("fill", "#a0d7c7"); // Lighten on hover
                                })
                                .on("mouseout", function() {
                                    d3.select(this).attr("fill", "#69b3a2"); // Revert color
                                });

                            // Add count labels above the bars
                            topGenreSvg.selectAll(".bar-label")
                                .data(genreData)
                                .enter()
                                .append("text")
                                .attr("class", "bar-label")
                                .attr("x", d => xScaleGenre(d.genre) + xScaleGenre.bandwidth() / 2) // Center horizontally
                                .attr("y", d => yScaleGenre(d.count) - 10) // Position slightly above the bar
                                .attr("dy", "0.35em") // Vertically center text
                                .text(d => d.count)
                                .attr("fill", "white")
                                .style("font-size", "15px");

                        } else {
                            console.error("Failed to fetch top genre data or no data found:", data.message);
                            topGenreContainer.append("p").text("No genre data available to display.").style("color", "white").style("text-align", "center");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching top genre data:", error);
                        topGenreContainer.append("p").text(`Error loading chart data: ${error.message}`).style("color", "red").style("text-align", "center");
                    });
            });
        </script>
    </body>
</html>
<?php include "../resources/footer.php"; ?>
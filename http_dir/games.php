<?php include "../resources/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Games View</title>
        <style>
            .games-body {
                background-color: #000123;
                background-attachment: fixed;
                margin: 0;
                padding: 0;
                font-family: 'Inter', sans-serif;
                color: white; /* Default text color for the body */
            }

            .games-container {
                min-width: 100vh;
                padding:2em;
            }

            .games-content {
                min-height: calc(100vh - 50px); /* Adjust for header/footer */
                display: flex;
                flex-direction: column;
                gap: 2em; /* Space between top and bottom sections */
                max-width: 100%; /* Max width for content */
                margin: 0 auto; /* Center the content */
            }

            .games-top {
                display: flex;
                flex-direction: column;
                align-items: center; /* Center horizontally */
            }

            .games-title h1 {
                font-size: 3em;
                margin-bottom: 0.5em;
                margin-top: 0px;
                color: #00bcd4; /* A light blue for the title */
                text-shadow: 0 0 10px rgba(0, 188, 212, 0.5); /* Subtle glow */
            }

            .games-search {
                width: 100%;
                max-width: 600px; /* Limit search bar width */
                position: relative; /* For results dropdown */
            }

            .games-search input[type="text"] {
                width: 100%;
                padding: 12px 20px;
                border: 2px solid #5a7d9b;
                border-radius: 25px;
                background-color: #3d6c8c;
                color: white;
                font-size: 1.1em;
                outline: none;
                transition: all 0.3s ease;
                box-sizing: border-box; /* Include padding in width */
            }

            .games-search input[type="text"]::placeholder {
                color: #bbb;
            }

            .games-search input[type="text"]:focus {
                border-color: #00bcd4;
                box-shadow: 0 0 15px rgba(0, 188, 212, 0.7);
            }

            /* Styles for the autocomplete suggestions dropdown */
            .games-suggestions {
                position: absolute;
                top: 100%; /* Position below the input */
                left: 0;
                right: 0;
                background-color: #3d6c8c; /* Darker background than search bar */
                border: 1px solid #5a7d9b;
                border-top: none;
                border-radius: 0 0 10px 10px;
                max-height: 200px;
                overflow-y: auto;
                z-index: 100;
                list-style: none;
                padding: 0;
                margin: 0;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            }

            .games-suggestions li {
                padding: 10px 20px;
                cursor: pointer;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                text-align: left;
                color: white;
            }

            .games-suggestions li:last-child {
                border-bottom: none;
            }

            .games-suggestions li:hover,
            .games-suggestions li.selected { /* For keyboard navigation */
                background-color: #5a7d9b;
            }

            .games-result {
                margin-top: 0.5em;
                text-align: center;
                color: #ccc;
                font-size: 0.9em;
                min-height: 20px; /* Reserve space for messages */
            }

            .games-bottom {
                display: flex;
                flex-wrap: wrap; /* Allow wrapping on smaller screens */
                gap: 2em;
                justify-content: center; /* Center items when wrapped */
                background-color: #2d8bba3a; /* Background for the game info block */
                border-radius: 15px;
                padding: 2em;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            }

            .games-poster {
                flex: 1 1 250px; /* Allows flexibility but minimum 250px width */
                max-width: 300px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1em;
                background-color: rgba(0,0,0,0.2); /* Semi-transparent background */
                padding: 1.5em;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
            }

            .games-poster img {
                max-width: 100%;
                height: auto;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            }

            .games-poster h4 {
                font-size: 1.8em;
                margin: 0;
                color: #ADD8E6; /* Lighter blue for game title */
            }

            .games-detail {
                flex: 2 1 400px; /* Allows flexibility but minimum 400px width, takes more space */
                background-color: rgba(0,0,0,0.2);
                padding: 1.5em;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
                text-align: left;
                line-height: 1.6;
            }

            .games-detail h4 {
                font-size: 1.2em;
                margin-top: 0;
                margin-bottom: 0.8em;
                color: #00bcd4; /* Matches title, for section headers */
                border-bottom: 1px solid rgba(255,255,255,0.1);
                padding-bottom: 0.5em;
            }

            .games-detail p {
                margin-bottom: 0.5em;
                font-size: 1em;
            }

            .games-detail strong {
                color: #ADD8E6;
                margin-right: 0.5em;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .games-bottom {
                    flex-direction: column; /* Stack poster and details vertically */
                    align-items: center;
                }
                .games-poster, .games-detail {
                    max-width: 100%; /* Take full width on small screens */
                    flex-basis: auto; /* Reset flex basis */
                }
                .games-poster {
                    order: 1; /* Poster first */
                }
                .games-detail {
                    order: 2; /* Details second */
                }
            }
        </style>
    </head>
    <body class="games-body">
        <div class="games-container">
            <main class="games-content">
                <div class="games-top">
                    <div class="games-title">
                        <h1>Games</h1>
                    </div>
                    <div class="games-search">
                        <input type="text" id="searchGames" placeholder="Search Game Name">
                        <!-- Autocomplete suggestions will appear here -->
                        <ul id="gameSuggestions" class="games-suggestions" style="display: none;"></ul>
                        <div id="resultGames" class="games-result">
                            <p>Start typing a game name to search...</p>
                        </div>
                    </div>
                </div>

                <div class="games-bottom">
                    <div class="games-poster">
                        <img id="gamePoster" src="https://placehold.co/400x300/cccccc/333333?text=Search+a+Game" alt="Game Poster">
                        <h4 id="gameTitle">Game Title</h4>
                    </div>
                    <div class="games-detail">
                        <p id="gameDescription"><strong>Description:</strong> No game selected.</p>
                        <p id="gameRating"><strong>Rating:</strong> N/A</p>
                        <p id="gameReleased"><strong>Released:</strong> N/A</p>
                        <p id="gameGenres"><strong>Genres:</strong> N/A</p>
                        <p id="gamePlatforms"><strong>Platforms:</strong> N/A</p>
                        <p id="gameDevelopers"><strong>Developers:</strong> N/A</p>
                        <p id="gamePublishers"><strong>Publishers:</strong> N/A</p>
                        <p id="gameMetacritic"><strong>Metacritic Rating:</strong> N/A</p>
                        <p id="gameESRB"><strong>ESRB Rating:</strong> N/A</p>
                    </div>
                </div>
            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchGames');
                const resultGamesDiv = document.getElementById('resultGames');
                const gameSuggestionsUl = document.getElementById('gameSuggestions'); // New element for suggestions
                const gamePosterImg = document.getElementById('gamePoster');
                const gameTitleH4 = document.getElementById('gameTitle');
                const gameDescriptionP = document.getElementById('gameDescription');
                const gameRatingP = document.getElementById('gameRating');
                const gameReleasedP = document.getElementById('gameReleased');
                const gameGenresP = document.getElementById('gameGenres');
                const gamePlatformsP = document.getElementById('gamePlatforms');
                const gameDevelopersP = document.getElementById('gameDevelopers');
                const gamePublishersP = document.getElementById('gamePublishers');
                const gameMetacriticP = document.getElementById('gameMetacritic');
                const gameESRBP = document.getElementById('gameESRB');

                let debounceTimer;
                let currentSuggestionIndex = -1; // For keyboard navigation

                // Function to display game details
                function displayGameDetails(game) {
                    if (game) {
                        gamePosterImg.src = game.ImageURL || 'https://placehold.co/400x300/cccccc/333333?text=No+Image+Available';
                        gamePosterImg.alt = game.Name;
                        gameTitleH4.textContent = game.Name;
                        gameDescriptionP.innerHTML = `<strong>Description:</strong> ${game.Description || 'No description available.'}`;
                        gameRatingP.innerHTML = `<strong>Rating:</strong> ${game.Rating || 'N/A'}`;
                        gameReleasedP.innerHTML = `<strong>Released:</strong> ${game.Released || 'N/A'}`;
                        gameGenresP.innerHTML = `<strong>Genres:</strong> ${game.Genre || 'N/A'}`;
                        gamePlatformsP.innerHTML = `<strong>Platforms:</strong> ${game.Platform || 'N/A'}`;
                        gameDevelopersP.innerHTML = `<strong>Developers:</strong> ${game.Developer || 'N/A'}`;
                        gamePublishersP.innerHTML = `<strong>Publishers:</strong> ${game.Publisher || 'N/A'}`;
                        gameMetacriticP.innerHTML = `<strong>Metacritic Rating:</strong> ${game.Metacritic || 'N/A'}`;
                        gameESRBP.innerHTML = `<strong>ESRB Rating:</strong> ${game.ESRB || 'N/A'}`;
                        resultGamesDiv.innerHTML = ''; // Clear search message on successful display
                        gameSuggestionsUl.style.display = 'none'; // Hide suggestions when a game is displayed
                    } else {
                        // Reset to default/empty state if no game is found or passed
                        gamePosterImg.src = 'https://placehold.co/400x300/cccccc/333333?text=Search+a+Game';
                        gamePosterImg.alt = 'Game Poster';
                        gameTitleH4.textContent = 'Game Title';
                        gameDescriptionP.innerHTML = '<strong>Description:</strong> No game selected or found.';
                        gameRatingP.innerHTML = '<strong>Rating:</strong> N/A';
                        gameReleasedP.innerHTML = '<strong>Released:</strong> N/A';
                        gameGenresP.innerHTML = '<strong>Genres:</strong> N/A';
                        gamePlatformsP.innerHTML = '<strong>Platforms:</strong> N/A';
                        gameDevelopersP.innerHTML = '<strong>Developers:</strong> N/A';
                        gamePublishersP.innerHTML = '<strong>Publishers:</strong> N/A';
                        gameMetacriticP.innerHTML = '<strong>Metacritic Rating:</strong> N/A';
                        gameESRBP.innerHTML = '<strong>ESRB Rating:</strong> N/A';
                        // Keep resultGamesDiv message if it's an error/no game found, otherwise clear
                        if (!resultGamesDiv.textContent.includes("Error:") && !resultGamesDiv.textContent.includes("not found")) {
                            resultGamesDiv.innerHTML = '<p>Start typing a game name to search...</p>';
                        }
                    }
                }

                // Function to fetch and display game details (main view)
                async function fetchAndDisplayGame(gameName) {
                    if (!gameName) { // Allow explicit null to clear display
                         displayGameDetails(null);
                         return;
                    }
                    if (gameName.length < 1) { // No minimum length for direct fetch (e.g. from suggestion click)
                        resultGamesDiv.innerHTML = '<p>Start typing a game name to search...</p>';
                        displayGameDetails(null);
                        return;
                    }

                    resultGamesDiv.innerHTML = '<p>Searching...</p>';
                    gameSuggestionsUl.style.display = 'none'; // Hide suggestions immediately when initiating a search

                    try {
                        // Use game_name parameter for full details
                        const response = await fetch(`../api/GamesView/get_games_data.php?game_name=${encodeURIComponent(gameName)}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const data = await response.json();

                        if (data.success && data.data) {
                            displayGameDetails(data.data);
                            resultGamesDiv.innerHTML = ''; // Clear search status
                        } else {
                            displayGameDetails(null); // Clear previous game if no new game found
                            resultGamesDiv.innerHTML = `<p style="color: red;">${data.message || 'Game not found.'}</p>`;
                        }
                    } catch (error) {
                        console.error("Error fetching game data:", error);
                        displayGameDetails(null);
                        resultGamesDiv.innerHTML = `<p style="color: red;">Error: ${error.message}. Please try again.</p>`;
                    }
                }

                // Function to fetch and render autocomplete suggestions
                async function fetchAndRenderSuggestions(query) {
                    gameSuggestionsUl.innerHTML = ''; // Clear previous suggestions
                    gameSuggestionsUl.style.display = 'none'; // Hide by default

                    if (query.length < 2) { // Require at least 2 characters for suggestions
                        resultGamesDiv.innerHTML = '<p>Keep typing to search for a game...</p>';
                        return;
                    }

                    try {
                        // Use search_query parameter for suggestions
                        const response = await fetch(`../api/GamesView/get_games_data.php?search_query=${encodeURIComponent(query)}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const data = await response.json();

                        if (data.success && data.data && data.data.length > 0) {
                            data.data.forEach((gameName, index) => {
                                const li = document.createElement('li');
                                li.textContent = gameName;
                                li.addEventListener('click', () => {
                                    searchInput.value = gameName; // Populate search bar
                                    fetchAndDisplayGame(gameName); // Fetch full details
                                    gameSuggestionsUl.style.display = 'none'; // Hide suggestions
                                    resultGamesDiv.innerHTML = ''; // Clear any search messages
                                });
                                gameSuggestionsUl.appendChild(li);
                            });
                            gameSuggestionsUl.style.display = 'block'; // Show suggestions
                            resultGamesDiv.innerHTML = ''; // Clear any search messages
                            currentSuggestionIndex = -1; // Reset selection
                        } else {
                            gameSuggestionsUl.style.display = 'none'; // Hide if no suggestions
                            resultGamesDiv.innerHTML = '<p>No suggestions found.</p>';
                        }
                    } catch (error) {
                        console.error("Error fetching suggestions:", error);
                        gameSuggestionsUl.style.display = 'none';
                        resultGamesDiv.innerHTML = `<p style="color: red;">Error fetching suggestions: ${error.message}</p>`;
                    }
                }

                // Event listener for search input
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();

                    if (query.length > 0) {
                        // Debounce for suggestions
                        debounceTimer = setTimeout(() => {
                            fetchAndRenderSuggestions(query);
                            // Removed the automatic fetchAndDisplayGame call here
                        }, 300); // Shorter debounce for suggestions
                    } else {
                        gameSuggestionsUl.style.display = 'none'; // Hide suggestions if input is empty
                        displayGameDetails(null); // Reset main display
                        resultGamesDiv.innerHTML = '<p>Start typing a game name to search...</p>';
                    }
                });

                // Handle keyboard navigation for suggestions (Arrow Up/Down, Enter)
                searchInput.addEventListener('keydown', function(e) {
                    const suggestions = gameSuggestionsUl.querySelectorAll('li');
                    if (suggestions.length === 0) return;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault(); // Prevent cursor movement in input
                        currentSuggestionIndex = (currentSuggestionIndex + 1) % suggestions.length;
                        highlightSuggestion(suggestions, currentSuggestionIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault(); // Prevent cursor movement in input
                        currentSuggestionIndex = (currentSuggestionIndex - 1 + suggestions.length) % suggestions.length;
                        highlightSuggestion(suggestions, currentSuggestionIndex);
                    } else if (e.key === 'Enter') {
                        e.preventDefault(); // Prevent form submission
                        if (currentSuggestionIndex > -1) {
                            suggestions[currentSuggestionIndex].click(); // Simulate click on selected suggestion
                        } else {
                            // If Enter is pressed without a selected suggestion, perform search for current input
                            fetchAndDisplayGame(searchInput.value.trim());
                        }
                    } else if (e.key === 'Escape') {
                        gameSuggestionsUl.style.display = 'none'; // Hide suggestions
                        currentSuggestionIndex = -1;
                    }
                });

                function highlightSuggestion(suggestions, index) {
                    suggestions.forEach((li, i) => {
                        li.classList.toggle('selected', i === index);
                    });
                    // Scroll to highlighted suggestion if off-screen
                    if (suggestions[index]) {
                        suggestions[index].scrollIntoView({ block: 'nearest' });
                    }
                }

                // Hide suggestions when clicking outside
                document.addEventListener('click', function(event) {
                    if (!searchInput.contains(event.target) && !gameSuggestionsUl.contains(event.target)) {
                        gameSuggestionsUl.style.display = 'none';
                    }
                });


                // Initial load: Check for game_name in URL
                const urlParams = new URLSearchParams(window.location.search);
                const initialGameName = urlParams.get('game_name');
                if (initialGameName) {
                    searchInput.value = decodeURIComponent(initialGameName);
                    fetchAndDisplayGame(decodeURIComponent(initialGameName));
                } else {
                    // Display initial prompt
                    displayGameDetails(null);
                    resultGamesDiv.innerHTML = '<p>Start typing a game name to search...</p>';
                }
            });
        </script>
    </body>
</html>
<?php include "../resources/footer.php"; ?>
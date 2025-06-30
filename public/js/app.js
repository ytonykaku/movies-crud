document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('movie-search-input');
    const resultsContainer = document.getElementById('search-results-container');
    const resultsList = document.getElementById('search-results-list');

    let debounceTimer;
    searchInput.addEventListener('keyup', (event) => {
        const query = event.target.value;

        clearTimeout(debounceTimer);

        if (query.length < 3) {
            resultsList.innerHTML = '';
            resultsContainer.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            performSearch(query);
        }, 500);
    });

    async function performSearch(query) {
        try {
            const response = await fetch(`/api/omdb/search?query=${encodeURIComponent(query)}`);
            const data = await response.json();

            displayResults(data.Search);
        } catch (error) {
            console.error('Erro ao buscar filmes:', error);
        }
    }

    function getRatingBySource(ratingsArray, sourceName) {
        if (!Array.isArray(ratingsArray)) {
            return 'N/A';
        }
        const ratingObject = ratingsArray.find(rating => rating.Source === sourceName);
        return ratingObject ? ratingObject.Value : 'N/A';
    }

    function displayResults(movies) {
        resultsList.innerHTML = '';

        if (!movies || movies.length === 0) {
            resultsContainer.classList.add('hidden');
            return;
        }

        movies.forEach(movie => {
            const listItem = document.createElement('li');

            listItem.dataset.imdbId = movie.imdbID; 
            listItem.className = 'p-2 hover:bg-gray-600 cursor-pointer';
            listItem.addEventListener('click', handleMovieSelect);
            
            listItem.innerHTML = `
                <div class="flex items-center">
                    <img src="${movie.Poster !== 'N/A' ? movie.Poster : 'https://via.placeholder.com/50x75?text=No+Image'}" 
                         alt="Pôster de ${movie.Title}" 
                         class="w-12 h-auto mr-4 rounded">
                    <div>
                        <h3 class="font-bold text-white">${movie.Title}</h3>
                        <p class="text-sm text-gray-400">${movie.Plot}</p>
                        <p class="text-sm text-gray-400">${movie.Genre}</p>
                        <p class="text-sm text-gray-400">${rottenTomatoesRating}>
                    </div>
                </div>
            `;
            resultsList.appendChild(listItem);
        });

        resultsContainer.classList.remove('hidden');
    }

    function handleMovieSelect(event) {
        const imdbID = event.currentTarget.dataset.imdbId; 
        
        console.log(`Filme selecionado! ID: ${imdbID}`);

        resultsContainer.classList.add('hidden');
        searchInput.value = '';

        // PRÓXIMO PASSO:
        // Aqui nós faremos uma nova chamada fetch para `/api/omdb/details?id=${imdbID}`
        // e usaremos os dados para abrir o modal do SweetAlert.
    }

});
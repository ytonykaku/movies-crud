document.addEventListener('DOMContentLoaded', () => {
    const ratedMoviesList = document.getElementById('rated-movies-list');
    if (ratedMoviesList) {
        ratedMoviesList.addEventListener('click', (event) => {
            const editButton = event.target.closest('.edit-button');
            const deleteButton = event.target.closest('.delete-button');

            if (editButton) {
                const ratedData = editButton.dataset;
                openRatingModal(null, ratedData);
            }

            if (deleteButton) {
                const ratedId = deleteButton.dataset.ratedId;
                handleDelete(ratedId);
            }
        });
    }
    
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
            if (!response.ok) throw new Error('A resposta da rede não foi ok');
            const data = await response.json();
            displayResults(data.results.Search);
        } catch (error) {
            console.error('Erro ao buscar filmes:', error);
            resultsList.innerHTML = '<li class="p-2 text-white">Erro ao buscar resultados.</li>';
            resultsContainer.classList.remove('hidden');
        }
    }

    function displayResults(movies) {
        resultsList.innerHTML = '';
        if (!movies || movies.length === 0) {
            resultsList.innerHTML = '<li class="p-2 text-white">Nenhum filme encontrado.</li>';
            resultsContainer.classList.remove('hidden');
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
                    <div><h3 class="font-bold text-white">${movie.Title} (${movie.Year})</h3></div>
                </div>`;
            resultsList.appendChild(listItem);
        });
        resultsContainer.classList.remove('hidden');
    }
    
    async function handleMovieSelect(event) {
        const imdbID = event.currentTarget.dataset.imdbId;
        resultsContainer.classList.add('hidden');
        searchInput.value = '';

        try {
            const response = await fetch(`/api/omdb/details?id=${imdbID}`);
            if (!response.ok) throw new Error('A resposta da rede não foi ok');
            const movieDetails = await response.json();
            openRatingModal(movieDetails);
        } catch (error) {
            console.error('Erro ao buscar detalhes do filme:', error);
            Swal.fire('Erro!', 'Não foi possível buscar os detalhes do filme.', 'error');
        }
    }

    async function openRatingModal(movieDetails, existingData = null) {
        const isEditing = existingData !== null;
        if (isEditing && !movieDetails) {
            try {
                const response = await fetch(`/api/omdb/details?id=${existingData.imdbId}`);
                if (!response.ok) throw new Error('Falha ao buscar detalhes para edição.');
                movieDetails = await response.json();
            } catch(error) {
                Swal.fire('Erro!', error.message, 'error');
                return;
            }
        }
        
        const rottenTomatoesRating = getRatingBySource(movieDetails.Ratings, 'Rotten Tomatoes');

        Swal.fire({
            title: `${movieDetails.Title} (${movieDetails.Year})`,
            html: `
                <div class="text-left">
                    <div class="flex flex-col md:flex-row">
                        <img src="${movieDetails.Poster !== 'N/A' ? movieDetails.Poster : 'https://via.placeholder.com/150x225?text=No+Image'}" 
                             alt="Pôster de ${movieDetails.Title}" class="w-48 mx-auto md:mx-0 md:mr-4 rounded mb-4 md:mb-0">
                        <div class="flex-1">
                            <p><strong>Gênero:</strong> ${movieDetails.Genre}</p>
                            <p><strong>Diretor:</strong> ${movieDetails.Director}</p>
                            <p class="mt-2"><strong>Rotten Tomatoes:</strong> ${rottenTomatoesRating}</p>
                            <p class="mt-2"><strong>Sinopse:</strong> ${movieDetails.Plot}</p>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div id="rating-form" class="flex flex-col">
                    <div class="flex justify-center">
                        <input id="swal-rate" class="swal2-input w-24 text-center" type="number" min="0" max="100" placeholder="Ex: 95" value="${isEditing ? existingData.currentRate : ''}">
                    </div>
                        <label for="swal-description" class="block mb-2 mt-4 text-center">Seu Comentário:</label>
                        <textarea id="swal-description" class="swal2-textarea w-full" placeholder="O que você achou do filme?">${isEditing ? existingData.currentDescription : ''}</textarea>
                    </div>
                </div>
                ${isEditing ? `<img id="modal-delete-button" src="/assets/trash.png" alt="Excluir" style="width:30px; cursor:pointer; margin-top:1rem; display:block; margin-left:auto; margin-right:auto;">` : ''}
            `,
            showCancelButton: true,
            confirmButtonText: isEditing ? 'Atualizar Avaliação' : 'Salvar Avaliação',
            cancelButtonText: 'Cancelar',
            width: '800px',
            didOpen: () => {
                if (isEditing) {
                    document.getElementById('modal-delete-button').addEventListener('click', () => {
                        handleDelete(existingData.ratedId);
                    });
                }
            },
            preConfirm: () => {
                const rate = document.getElementById('swal-rate').value;
                const description = document.getElementById('swal-description').value;
                if (!rate || rate < 0 || rate > 100) {
                    Swal.showValidationMessage('Por favor, insira uma nota válida entre 0 e 100.');
                    return false;
                }

                const formData = new FormData();
                const url = isEditing ? '/api/rated-movies/update' : '/api/rated-movies/create';
                
                if (isEditing) {
                    formData.append('id', existingData.ratedId);
                } else {
                    formData.append('imdbId', movieDetails.imdbID);
                    formData.append('title', movieDetails.Title);
                    formData.append('plot', movieDetails.Plot);
                    formData.append('genre', movieDetails.Genre);
                    formData.append('ratings', getRatingBySource(movieDetails.Ratings, 'Internet Movie Database'));
                }
                formData.append('rate', rate);
                formData.append('description', description);

                return fetch(url, { method: 'POST', body: formData })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message) });
                        }
                        return response.json();
                    })
                    .catch(error => Swal.showValidationMessage(`Falha ao salvar: ${error.message}`));
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Sucesso!', result.value.message, 'success')
                    .then(() => location.reload());
            }
        });
    }

    function handleDelete(ratedId) {
        Swal.fire({
            title: 'Você tem certeza?',
            text: "Você não poderá reverter esta ação!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', ratedId);

                fetch('/api/rated-movies/delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Excluído!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Erro!', data.message, 'error');
                    }
                })
                .catch(error => Swal.fire('Erro!', 'Falha na comunicação com o servidor.', 'error'));
            }
        });
    }
    
    function getRatingBySource(ratingsArray, sourceName) {
        if (!Array.isArray(ratingsArray)) return 'N/A';
        const ratingObject = ratingsArray.find(rating => rating.Source === sourceName);
        return ratingObject ? ratingObject.Value : 'N/A';
    }

    const showDeletedBtn = document.getElementById('show-deleted-list');
        if (showDeletedBtn) {
            showDeletedBtn.addEventListener('click', () => {
                openDeletedMoviesModal();
            });
        }

    function openDeletedMoviesModal() {
        const deletedMoviesContainer = document.getElementById('deleted-movies-data');
        const deletedItems = deletedMoviesContainer.querySelectorAll('.deleted-item');

        let itemsHtml = '<p class="text-center">Nenhum filme na lixeira.</p>';

        if (deletedItems.length > 0) {
            itemsHtml = '<ul class="text-left space-y-3">';
            deletedItems.forEach(item => {
                itemsHtml += `
                    <li class="flex items-center justify-between p-2 bg-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <img src="${item.dataset.poster}" alt="Poster" class="w-12 rounded mr-3">
                            <span>${item.dataset.title}</span>
                        </div>
                        <button class="restore-button bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded" data-id="${item.dataset.id}">
                            Restaurar
                        </button>
                    </li>
                `;
            });
            itemsHtml += '</ul>';
        }

        Swal.fire({
            title: 'Filmes Excluídos',
            html: itemsHtml,
            width: '600px',
            showConfirmButton: false,
            showCloseButton: true,
            didOpen: (modal) => {
                modal.querySelectorAll('.restore-button').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const ratedId = event.currentTarget.dataset.id;
                        handleRestore(ratedId);
                    });
                });
            }
        });
    }

    function handleRestore(ratedId) {
        const formData = new FormData();
        formData.append('id', ratedId);

        fetch('/api/rated-movies/restore', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Restaurado!', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Erro!', data.message, 'error');
            }
        })
        .catch(error => Swal.fire('Erro!', 'Falha na comunicação com o servidor.', 'error'));
    }

});
export function initBookFilter(books, renderCallback) {
  const titleInput = document.getElementById('filter-title');
  const authorSelect = document.getElementById('filter-author');
  const genreSelect = document.getElementById('filter-genre');

  // Popola dinamicamente autori e generi
  const authors = [...new Set(books.map(b => b.author))].sort();
  const genres = [...new Set(books.map(b => b.genre))].sort();

  authors.forEach(author => {
    const opt = document.createElement('option');
    opt.value = author;
    opt.textContent = author;
    authorSelect.appendChild(opt);
  });

  genres.forEach(genre => {
    const opt = document.createElement('option');
    opt.value = genre;
    opt.textContent = genre;
    genreSelect.appendChild(opt);
  });

  function filterBooks() {
    const filtered = books.filter(book => {
      return (
        (!titleInput.value || book.title.toLowerCase().includes(titleInput.value.toLowerCase())) &&
        (!authorSelect.value || book.author === authorSelect.value) &&
        (!genreSelect.value || book.genre === genreSelect.value)
      );
    });
    renderCallback(filtered);
  }

  // Eventi
  titleInput.addEventListener('input', filterBooks);
  authorSelect.addEventListener('change', filterBooks);
  genreSelect.addEventListener('change', filterBooks);

  // Mostra tutti i libri inizialmente
  renderCallback(books);
}
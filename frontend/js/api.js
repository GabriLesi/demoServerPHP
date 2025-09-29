const API_BASE = "http://localhost:8000"; // In base alla porta in cui viene lanciato localmente

// Ottieni tutti i libri
async function getBooks() {
  const res = await fetch(`${API_BASE}/books`);
  if (!res.ok) throw new Error("Errore caricamento libri");
  return await res.json();
}

// Ottieni dettaglio libro
async function getBook(bookID) {
  const res = await fetch(`${API_BASE}/books/${bookID}`);
  if (!res.ok) throw new Error("Libro non trovato");
  return await res.json();
}

// Ottieni dettagli del proprio utente
async function getUser() {
  const res = await fetch(`${API_BASE}/user`);
  if (!res.ok) throw new Error("Utente non trovato");
  return await res.json();
}

// Ottieni libri associati al proprio utente
async function getUserBooks() {
  const res = await fetch(`${API_BASE}/userbooks`);
  if (!res.ok) throw new Error("Errore caricamento libri utente");
  return await res.json();
}
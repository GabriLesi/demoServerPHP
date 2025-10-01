const API_BASE = "http://localhost:8000"; // In base alla porta in cui viene lanciato localmente

// Check login
async function checkAuth() {
  const res = await fetch("http://localhost:8000/me", { credentials: "same-origin" });
  if (!res.ok) {
    window.location.href = "login.html";
    return null;
  }
  return await res.json();
}

async function logout() {
  fetch("http://localhost:8000/logout").then(() => {
    window.location.href = "login.html";
  });
}

async function addUserBook(bookId) {
  const res = await fetch('http://localhost:8000/userbooks', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ bookID: bookId }),
    credentials: 'same-origin'
  });
  return await res.json();
}

async function removeUserBook(bookId) {
  const res = await fetch('http://localhost:8000/userbooks', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ bookID: bookId }),
    credentials: 'same-origin'
  });
  return await res.json();
}

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
  const res = await fetch(`${API_BASE}/me`);
  if (!res.ok) throw new Error("Utente non trovato");
  return await res.json();
}

// Ottieni libri associati al proprio utente
async function getUserBooks() {
  const res = await fetch(`${API_BASE}/userbooks`);
  if (!res.ok) throw new Error("Errore caricamento libri utente");
  return await res.json();
}
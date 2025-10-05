// common/loader.js
let activeRequests = 0;
let loaderInitialized = false;

function showLoader() {
  const loader = document.getElementById("page-loader");
  if (loader) loader.style.display = "flex";
}

function hideLoader() {
  const loader = document.getElementById("page-loader");
  if (loader) loader.style.display = "none";
}

// Wrapper per fetch globale
const originalFetch = window.fetch;
window.fetch = async function (...args) {
  if (!loaderInitialized) return originalFetch(...args);

  activeRequests++;
  showLoader();

  try {
    const response = await originalFetch(...args);
    return response;
  } finally {
    activeRequests--;
    if (activeRequests <= 0) {
      // Attendi un po’ prima di nascondere per evitare flicker
      setTimeout(() => {
        if (activeRequests === 0) {
          hideLoader();
          document.getElementById("page-content").style.display = "block";
        }
      }, 400);
    }
  }
};

// Inizializza il loader dopo che loader.html è stato caricato
async function initLoader() {
  const container = document.getElementById("loader-container");
  if (!container) return;

  const res = await fetch("common/loader.html");
  const html = await res.text();
  container.innerHTML = html;
  loaderInitialized = true;
  showLoader();
}
document.addEventListener('DOMContentLoaded', function () {

// Data produk - overide php if exist
let PRODUCTS = window.PRODUCTS || [
  { id: 'sabrina3', title: 'Sabrina 3 EP', brand: 'NIKE', img: 'img/products/p1.jpg' },
  { id: 'jelly', title: 'Ja 3 Jelly Bean', brand: 'NIKE', img: 'img/products/p2.jpg' },
  { id: 'subzone', title: 'Subzone', brand: 'ADIDAS', img: 'img/products/p3.jpg' },
  { id: 'radiant', title: 'Sabrina 3 Radiant EP', brand: 'NIKE', img: 'img/products/p4.jpg' },
  { id: 'mvp', title: 'Jumpman MVP', brand: 'NIKE', img: 'img/products/p5.jpg' },
  { id: 'world', title: 'MB 05 World Tour', brand: 'PUMA', img: 'img/products/p6.jpg' },
  { id: 'reaper', title: 'KD18 Slim Reaper EP', brand: 'NIKE', img: 'img/products/p7.jpg' },
  { id: 'alien', title: 'MB 04 Lo Alien Skins', brand: 'PUMA', img: 'img/products/p8.jpg' },
  { id: 'vintage', title: 'Air Force 1 07 Vintage', brand: 'NIKE', img: 'img/products/q1.jpg' },
  { id: 'ballet', title: 'Suede Speedcat Ballet', brand: 'PUMA', img: 'img/products/q2.jpg' },
  { id: 'edge', title: 'Air Jordan 40 Edge PF', brand: 'NIKE', img: 'img/products/q3.jpg' },
  { id: 'max', title: 'Air Max 90', brand: 'NIKE', img: 'img/products/q4.jpg' },
  { id: 'kitty', title: 'Puma x Hello Kitty Kids', brand: 'PUMA', img: 'img/products/q5.jpg' },
  { id: 'talk', title: 'Street Talk Shoes Kids', brand: 'ADIDAS', img: 'img/products/q6.jpg' },
  { id: 'luxe', title: 'Superstar Boot Luxe TC', brand: 'ADIDAS', img: 'img/products/q7.jpg' },
  { id: 'galaxy', title: 'Galaxy 7 Running Shoes', brand: 'ADIDAS', img: 'img/products/q8.jpg' }
];

// Helper function
function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, c => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
  }[c]));
}

// Search navbar
const navInput = document.getElementById('navSearchInput');
const navBtn = document.getElementById('navSearchBtn');
const resultsBox = document.getElementById('navSearchResults');

if (navInput && resultsBox) {
  function buildItem(p) {
    const link = document.createElement('a');
    link.href = 'product.php?id=' + encodeURIComponent(p.id);
    link.style.display = 'block';
    link.style.textDecoration = 'none';
    link.style.color = 'inherit';
    link.setAttribute('role', 'option');

    const item = document.createElement('div');
    item.style.display = 'flex';
    item.style.gap = '10px';
    item.style.alignItems = 'center';
    item.style.padding = '8px';
    item.style.cursor = 'pointer';
    item.style.borderBottom = '1px solid rgba(0,0,0,0.04)';

    const img = document.createElement('img');
    img.src = p.img;
    img.alt = p.title;
    img.style.width = '56px';
    img.style.height = '56px';
    img.style.objectFit = 'cover';
    img.style.borderRadius = '6px';
    img.style.flex = '0 0 56px';

    const txt = document.createElement('div');
    txt.style.flex = '1';
    txt.innerHTML = `<div style="font-weight:700">${escapeHtml(p.brand)} - ${escapeHtml(p.title)}</div>
                     <div style="font-size:13px;color:#555">ID: ${escapeHtml(p.id)}</div>`;

    item.appendChild(img);
    item.appendChild(txt);
    link.appendChild(item);
    return link;
  }

  function showResults(list) {
    resultsBox.innerHTML = '';
    if (!list || list.length === 0) {
      const e = document.createElement('div');
      e.style.padding = '12px';
      e.style.color = '#666';
      e.textContent = 'No products found.';
      resultsBox.appendChild(e);
      resultsBox.style.display = 'block';
      return;
    }
    list.forEach(p => resultsBox.appendChild(buildItem(p)));
    resultsBox.style.display = 'block';
  }

  function hideResults() {
    resultsBox.style.display = 'none';
  }

  function doSearch() {
    const q = (navInput.value || '').trim().toLowerCase();
    if (!q) {
      hideResults();
      return;
    }
    const filtered = PRODUCTS.filter(p =>
      (p.title + ' ' + p.brand + ' ' + p.id).toLowerCase().includes(q)
    );
    showResults(filtered);
  }

  navInput.addEventListener('input', doSearch);
  navInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const q = (navInput.value || '').trim().toLowerCase();
      if (!q) return;
      const first = PRODUCTS.find(p =>
        (p.title + ' ' + p.brand + ' ' + p.id).toLowerCase().includes(q)
      );
      if (first) window.location.href = 'product.php?id=' + encodeURIComponent(first.id);
    }
  });

  navBtn.addEventListener('click', function () {
    doSearch();
    const first = resultsBox.querySelector('[role="option"]');
    if (first) first.click();
  });

  document.addEventListener('click', function (e) {
    const navLi = document.querySelector('.nav-search');
    if (!navLi) return;
    if (!navLi.contains(e.target)) hideResults();
  });
}

// Direct product helpers
window.openProduct = function(productId) {
  window.location.href = 'product.php?id=' + encodeURIComponent(productId);
};

document.querySelectorAll('.pro').forEach(function (card) {
  const id = card.getAttribute('data-id');
  if (!id) return;
  if (card.dataset.listenerAttached === '1') return;
  card.addEventListener('click', function (e) {
    if (e.target.closest('a')) return;
    openProduct(id);
  });
  card.dataset.listenerAttached = '1';
});

// Back button helper
window.goBack = function () {
  if (window.history.length > 1) {
    window.history.back();
  } else {
    window.location.href = 'shop.php';
  }
};

// Pagination for shop page
if (window.location.pathname.includes('shop.php') || window.location.pathname.endsWith('/shop')) {
  const perPage = 8;
  const urlParams = new URLSearchParams(window.location.search);
  let currentPage = Math.max(1, Number(urlParams.get('page')) || 1);

  let allPro = Array.from(document.querySelectorAll('#product1 .pro-con .pro'));
  if (!allPro.length) allPro = Array.from(document.querySelectorAll('.pro'));
  const totalItems = allPro.length;
  const totalPages = Math.max(1, Math.ceil(totalItems / perPage));

  function showPage(page) {
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    currentPage = page;

    allPro.forEach(el => (el.style.display = 'none'));
    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const pageItems = allPro.slice(start, end);
    pageItems.forEach(el => (el.style.display = ''));

    document.querySelectorAll('.pagination button[data-page]').forEach(b => {
      b.classList.remove('active');
      if (Number(b.dataset.page) === currentPage) b.classList.add('active');
    });

    const newParams = new URLSearchParams(window.location.search);
    newParams.set('page', String(currentPage));
    history.replaceState(null, '', window.location.pathname + '?' + newParams.toString());
  }

  document.querySelectorAll('.pagination button[data-page]').forEach(btn => {
    btn.addEventListener('click', function () {
      const p = Number(btn.dataset.page) || 1;
      showPage(p);
      const header = document.getElementById('page-header') || document.getElementById('product1');
      if (header) header.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  const nextBtn = document.querySelector('.pagination button.next') ||
    document.querySelector('.pagination button[aria-label="next"]') ||
    Array.from(document.querySelectorAll('.pagination button')).reverse().find(b => !b.hasAttribute('data-page'));

  if (nextBtn) {
    nextBtn.addEventListener('click', function () {
      if (currentPage < totalPages) {
        showPage(currentPage + 1);
        const header = document.getElementById('page-header') || document.getElementById('product1');
        if (header) header.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  if (currentPage > totalPages) currentPage = totalPages;
  showPage(currentPage);
}

});
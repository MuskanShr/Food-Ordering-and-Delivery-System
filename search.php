
<style>
.search-page {
    padding: 3rem;
    max-width: 900px;
    margin: 0 auto;
    min-height: calc(100vh - 64px - 300px); /* add this */
}

.search-page h1 {
    font-family:'Playfair Display',serif;
    font-size:2rem; font-weight:800; margin-bottom:1.5rem;
}
.search-bar-wrap { display:flex; gap:0.75rem; margin-bottom:2rem; }
.search-input {
    flex:1; padding:0.85rem 1.2rem;
    border:2px solid var(--border); border-radius:12px;
    font-family:'DM Sans',sans-serif; font-size:1rem;
    outline:none; transition:border 0.2s; background:white;
}
.search-input:focus { border-color:var(--orange); }
.search-btn {
    background:var(--orange); color:white;
    border:none; border-radius:12px;
    padding:0 1.5rem; font-size:1rem; font-weight:700;
    cursor:pointer; font-family:'DM Sans',sans-serif;
}
.search-btn:hover { background:var(--orange-dark); }

.results-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(220px,1fr));
    gap:1.5rem;
}
.result-card {
    background:white; border-radius:14px;
    border:1px solid var(--border); overflow:hidden;
    box-shadow:var(--shadow); transition:all 0.3s;
}
.result-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-lg); }
.result-img {
    height:130px;
    background:linear-gradient(135deg,#FFF0E6,#FFE4CC);
    display:flex; align-items:center; justify-content:center;
    font-size:3rem; overflow:hidden;
}
.result-img img { width:100%; height:100%; object-fit:cover; }
.result-body { padding:0.9rem; }
.result-name { font-weight:700; font-size:0.95rem; margin-bottom:0.2rem; }
.result-cat { font-size:0.76rem; color:var(--orange); font-weight:600; margin-bottom:0.4rem; }
.result-desc {
    font-size:0.8rem; color:var(--gray); line-height:1.5; margin-bottom:0.7rem;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
.result-footer { display:flex; align-items:center; justify-content:space-between; }
.result-price { font-weight:700; color:var(--orange); }
.btn-add-small {
    background:var(--orange); color:white;
    border:none; border-radius:7px;
    padding:0.32rem 0.75rem; font-size:0.78rem; font-weight:600;
    cursor:pointer; font-family:'DM Sans',sans-serif; text-decoration:none;
}
.btn-add-small:hover { background:var(--orange-dark); }
.no-results { text-align:center; padding:3rem; color:var(--gray); }
</style>

<div class="search-page">
    <h1>Search Menu</h1>
    <form method="GET" class="search-bar-wrap">
        <input type="text" name="q" id="searchInput" class="search-input"
               placeholder="Search for chicken, pizza, drinks…"
               value="<?= htmlspecialchars($query) ?>" autocomplete="off">
        <button type="submit" class="search-btn">Search</button>
    </form>

    <div id="resultsContainer">
    <?php if($query): ?>
        <?php if(empty($items)): ?>
            <div class="no-results"><p>No results for "<?= htmlspecialchars($query) ?>"</p></div>
        <?php else: ?>
            <p style="margin-bottom:1rem;color:var(--gray);font-size:0.9rem;"><?= count($items) ?> result(s) for "<?= htmlspecialchars($query) ?>"</p>
            <div class="results-grid">
                <?php foreach($items as $item): ?>
                <div class="result-card">
                    <div class="result-img">
                        <?php if($item['image'] && file_exists('uploads/'.$item['image'])): ?>
                            <img src="/foodbyte/uploads/<?= htmlspecialchars($item['image']) ?>" alt="">
                        <?php else: ?><?php endif; ?>
                    </div>
                    <div class="result-body">
                        <div class="result-cat"><?= htmlspecialchars($item['cat_name']) ?></div>
                        <div class="result-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="result-desc"><?= htmlspecialchars($item['description']) ?></div>
                        <div class="result-footer">
                            <span class="result-price">Rs <?= number_format($item['price'],0) ?></span>
                            <a href="/foodorder/cart.php?add=<?= $item['id'] ?>" class="btn-add-small">Add to cart</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="no-results"><p>Start typing to search our menu</p></div>
    <?php endif; ?>
    </div>
</div>

<script>
let timer;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) return;
    timer = setTimeout(() => {
        fetch('?q=' + encodeURIComponent(q) + '&ajax=1')
        .then(r => r.json())
        .then(items => {
            const container = document.getElementById('resultsContainer');
            if (!items.length) {
                container.innerHTML = '<div class="no-results"><p>No results found</p></div>';
                return;
            }
            let html = `<p style="margin-bottom:1rem;color:var(--gray);font-size:0.9rem;">${items.length} result(s)</p><div class="results-grid">`;
           items.forEach(item => {
    const imgHtml = item.image 
        ? `<img src="/foodbyte/uploads/${item.image}" alt="">` 
        : '';
    html += `
    <div class="result-card">
        <div class="result-img">${imgHtml}</div>
        <div class="result-body">
            <div class="result-cat">${item.cat_name}</div>
            <div class="result-name">${item.name}</div>
            <div class="result-desc">${item.description}</div>
            <div class="result-footer">
                <span class="result-price">Rs ${parseInt(item.price).toLocaleString()}</span>
                <a href="/foodorder/cart.php?add=${item.id}" class="btn-add-small">Add to cart</a>
            </div>
        </div>
    </div>`;
});
            html += '</div>';
            container.innerHTML = html;
        });
    }, 300);
});
</script>

<?php include 'includes/footer.php'; ?>
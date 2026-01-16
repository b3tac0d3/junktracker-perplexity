<?php
/** @var array $results */
/** @var string $query */
/** @var string $type */
?>

<div class="search-page">

    <!-- Search Header -->
    <div class="search-header">
        <h1 class="search-title">Search</h1>
        <p class="search-subtitle">Search across clients, jobs, sales, and users</p>
    </div>

    <!-- Search Bar -->
    <div class="search-bar-container">
        <!-- Use baseUrl from the configuration to build the correct action path -->
        <form method="GET" action="<?= $baseUrl ?>/search" class="search-form">
            <div class="search-input-wrapper">
                <input 
                    type="text" 
                    name="q" 
                    id="searchInput" 
                    class="search-input" 
                    placeholder="Search for clients, jobs, sales, users..."
                    value="<?php echo htmlspecialchars($query ?? ''); ?>"
                    autofocus
                >
                <button type="submit" class="search-button">
                    🔍 Search
                </button>
            </div>
            
            <!-- Filter buttons -->
            <div class="search-filters">
                <label class="filter-label">Filter by:</label>
                <div class="search-filter-buttons">
                    <button type="button" class="filter-btn <?php echo (!isset($type) || $type === 'all') ? 'active' : ''; ?>" data-type="all">All</button>
                    <button type="button" class="filter-btn <?php echo ($type === 'clients') ? 'active' : ''; ?>" data-type="clients">Clients</button>
                    <button type="button" class="filter-btn <?php echo ($type === 'jobs') ? 'active' : ''; ?>" data-type="jobs">Jobs</button>
                    <button type="button" class="filter-btn <?php echo ($type === 'sales') ? 'active' : ''; ?>" data-type="sales">Sales</button>
                    <button type="button" class="filter-btn <?php echo ($type === 'users') ? 'active' : ''; ?>" data-type="users">Users</button>
                </div>
                <input type="hidden" name="type" id="searchType" value="<?php echo htmlspecialchars($type ?? 'all'); ?>">
            </div>
        </form>
    </div>

    <!-- Search Results -->
    <div class="search-results">
        <?php if (isset($query) && !empty($query)): ?>
            <?php if (!empty($results)): ?>
                <div class="search-results-header">
                    <p class="search-results-count">
                        Found <?php echo count($results); ?> result(s) for "<?php echo htmlspecialchars($query); ?>"
                    </p>
                </div>

                <div class="search-results-list">
                    <?php foreach ($results as $result): ?>
                        <div class="search-result-item" data-type="<?php echo htmlspecialchars($result['type']); ?>">
                            <div class="result-header">
                                <span class="result-type-badge result-type-<?php echo htmlspecialchars($result['type']); ?>">
                                    <?php echo ucfirst($result['type']); ?>
                                </span>
                                <h3 class="result-title">
                                    <a href="<?php echo htmlspecialchars($result['url']); ?>">
                                        <?php echo htmlspecialchars($result['title']); ?>
                                    </a>
                                </h3>
                            </div>
                            <p class="result-description">
                                <?php echo htmlspecialchars($result['description']); ?>
                            </p>
                            <?php if (!empty($result['meta'])): ?>
                                <div class="result-meta">
                                    <?php echo htmlspecialchars($result['meta']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="search-no-results">
                    <div class="no-results-icon">🔍</div>
                    <h3>No results found</h3>
                    <p>We couldn't find any results for "<?php echo htmlspecialchars($query); ?>".</p>
                    <p class="no-results-hint">Try using different keywords or check your spelling.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="search-empty-state">
                <div class="empty-state-icon">🔍</div>
                <h3>Start searching</h3>
                <p>Enter a search term above to find clients, jobs, sales, or users.</p>
                <div class="search-tips">
                    <h4>Search tips:</h4>
                    <ul>
                        <li>Use specific keywords for better results</li>
                        <li>Filter by category to narrow your search</li>
                        <li>Try searching by name, email, or ID</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchTypeInput = document.getElementById('searchType');
    const searchForm = document.querySelector('.search-form');
    const resultItems = document.querySelectorAll('.search-result-item');

    // Handle filter button clicks
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const type = this.getAttribute('data-type');
            searchTypeInput.value = type;

            // If there are results, filter them client-side
            if (resultItems.length > 0) {
                resultItems.forEach(item => {
                    const itemType = item.getAttribute('data-type');
                    if (type === 'all' || itemType === type) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            } else {
                // If no results yet, submit the form to search with filter
                const searchInput = document.getElementById('searchInput');
                if (searchInput.value.trim()) {
                    searchForm.submit();
                }
            }
        });
    });

    // Handle search form submission
    searchForm.addEventListener('submit', function (e) {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput.value.trim()) {
            e.preventDefault();
            alert('Please enter a search term');
            searchInput.focus();
        }
    });
});
</script>

<style>
.search-page {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.search-header {
    margin-bottom: 2rem;
}

.search-title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.search-subtitle {
    color: #666;
    font-size: 1rem;
}

.search-bar-container {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.search-input-wrapper {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    font-size: 1rem;
}

.search-input:focus {
    outline: none;
    border-color: #4CAF50;
}

.search-button {
    padding: 0.75rem 2rem;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    cursor: pointer;
}

.search-button:hover {
    background: #45a049;
}

.search-filters {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.filter-label {
    font-weight: 500;
    color: #666;
}

.search-filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.search-results-header {
    margin-bottom: 1rem;
}

.search-results-count {
    color: #666;
    font-size: 0.9rem;
}

.search-results-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.search-result-item {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.result-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.result-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.result-type-clients { background: #e3f2fd; color: #1976d2; }
.result-type-jobs { background: #fff3e0; color: #f57c00; }
.result-type-sales { background: #e8f5e9; color: #388e3c; }
.result-type-users { background: #f3e5f5; color: #7b1fa2; }

.result-title {
    margin: 0;
    font-size: 1.25rem;
}

.result-title a {
    color: #333;
    text-decoration: none;
}

.result-title a:hover {
    color: #4CAF50;
}

.result-description {
    color: #666;
    margin: 0.5rem 0;
}

.result-meta {
    color: #999;
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

.search-empty-state,
.search-no-results {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-state-icon,
.no-results-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.search-empty-state h3,
.search-no-results h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.search-tips {
    text-align: left;
    max-width: 400px;
    margin: 2rem auto 0;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 6px;
}

.search-tips h4 {
    margin-top: 0;
}

.search-tips ul {
    margin: 0.5rem 0 0;
    padding-left: 1.5rem;
}

.no-results-hint {
    color: #999;
    font-size: 0.9rem;
}
</style>

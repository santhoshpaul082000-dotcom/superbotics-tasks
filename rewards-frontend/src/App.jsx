
import { useEffect, useState } from "react";
import "./App.css";

const API = "http://127.0.0.1:8000/api";

function App() {
  const [userId, setUserId] = useState("1");
  const [balance, setBalance] = useState(0);
  const [products, setProducts] = useState([]);
  const [search, setSearch] = useState("");
  const [category, setCategory] = useState("All");
  const [loading, setLoading] = useState(true);
  const [redeemingId, setRedeemingId] = useState(null);
  const [message, setMessage] = useState(null);

  const fetchBalance = async () => {
    try {
      const response = await fetch(`${API}/users/${userId}/balance`, {
        headers: { Accept: "application/json" },
      });

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.message || "Unable to fetch balance");
      }

      const value =
        result.balance ??
        result.data?.balance ??
        result.data ??
        0;

      setBalance(
        typeof value === "object"
          ? Number(value.balance ?? 0)
          : Number(value)
      );
    } catch (error) {
      setMessage({ type: "error", text: error.message });
    }
  };

  const fetchProducts = async () => {
    try {
      setLoading(true);

      const response = await fetch(`${API}/products`, {
        headers: { Accept: "application/json" },
      });

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.message || "Unable to load products");
      }

      setProducts(result.data || []);
    } catch (error) {
      setMessage({ type: "error", text: error.message });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  useEffect(() => {
    if (userId) {
      fetchBalance();
    }
  }, [userId]);

  const redeemProduct = async (productId) => {
    try {
      setRedeemingId(productId);
      setMessage(null);

      const response = await fetch(`${API}/redemptions`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          user_id: Number(userId),
          product_id: productId,
        }),
      });

      const result = await response.json();

      if (!response.ok) {
        throw new Error(
          result.message || "Redemption failed. Please try again."
        );
      }

      setMessage({
        type: "success",
        text: result.message || "Product redeemed successfully!",
      });

      await fetchBalance();
    } catch (error) {
      setMessage({ type: "error", text: error.message });
    } finally {
      setRedeemingId(null);
    }
  };

  const categories = [
    "All",
    ...new Set(products.map((product) => product.category?.name).filter(Boolean)),
  ];

  const filteredProducts = products.filter((product) => {
    const matchesSearch = product.name
      .toLowerCase()
      .includes(search.toLowerCase());

    const matchesCategory =
      category === "All" || product.category?.name === category;

    return matchesSearch && matchesCategory;
  });

  return (
    <div className="app">
      <header className="topbar">
        <div className="brand">
          <div className="brand-icon">R</div>
          <span>Rewardly</span>
        </div>

        <div className="user-control">
          <label htmlFor="userId">User ID</label>
          <input
            id="userId"
            type="number"
            min="1"
            value={userId}
            onChange={(event) => setUserId(event.target.value)}
          />
        </div>
      </header>

      <main className="main-content">
        <section className="welcome-section">
          <div>
            <p className="eyebrow">REWARDS STORE</p>
            <h1>Redeem your rewards.</h1>
            <p className="subtitle">
              Explore products and exchange your points for rewards.
            </p>
          </div>

          <div className="balance-card">
            <div className="balance-icon">✦</div>
            <div>
              <p>Available balance</p>
              <h2>{balance.toLocaleString("en-IN")} <span>pts</span></h2>
            </div>
          </div>
        </section>

        {message && (
          <div className={`alert ${message.type}`} role="alert">
            <span>{message.type === "success" ? "✓" : "!"}</span>
            {message.text}
            <button
              className="alert-close"
              onClick={() => setMessage(null)}
              aria-label="Dismiss message"
            >
              ×
            </button>
          </div>
        )}

        <section className="catalogue-section">
          <div className="section-heading">
            <div>
              <p className="eyebrow">THE COLLECTION</p>
              <h2>Explore rewards</h2>
              <p className="subtitle">
                Choose something you love.
              </p>
            </div>

            <span className="product-count">
              {filteredProducts.length} products
            </span>
          </div>

          <div className="toolbar">
            <div className="search-box">
              <span>⌕</span>
              <input
                type="search"
                placeholder="Search products..."
                value={search}
                onChange={(event) => setSearch(event.target.value)}
              />
            </div>

            <select
              value={category}
              onChange={(event) => setCategory(event.target.value)}
              aria-label="Filter by category"
            >
              {categories.map((item) => (
                <option key={item} value={item}>
                  {item === "All" ? "All categories" : item}
                </option>
              ))}
            </select>
          </div>

          {loading ? (
            <div className="empty-state">
              <div className="loader"></div>
              <p>Loading rewards...</p>
            </div>
          ) : filteredProducts.length === 0 ? (
            <div className="empty-state">
              <div className="empty-icon">⌕</div>
              <h3>No products found</h3>
              <p>Try another search or category.</p>
            </div>
          ) : (
            <div className="product-grid">
              {filteredProducts.map((product) => {
                const affordable =
                  balance >= Number(product.points_required);

                return (
                  <article className="product-card" key={product.id}>
                    <div className="product-art">
                      <span className="product-symbol">✦</span>
                      <span className="product-category">
                        {product.category?.name || "Reward"}
                      </span>
                    </div>

                    <div className="product-info">
                      <h3>{product.name}</h3>

                      <div className="product-footer">
                        <div>
                          <p className="points-label">REQUIRED POINTS</p>
                          <strong>
                            {Number(product.points_required).toLocaleString("en-IN")}
                            <span> pts</span>
                          </strong>
                        </div>

                        <button
                          className={`redeem-button ${!affordable ? "disabled" : ""}`}
                          disabled={!affordable || redeemingId !== null}
                          onClick={() => redeemProduct(product.id)}
                        >
                          {redeemingId === product.id
                            ? "Redeeming..."
                            : affordable
                              ? "Redeem"
                              : "Not enough"}
                        </button>
                      </div>
                    </div>
                  </article>
                );
              })}
            </div>
          )}
        </section>
      </main>

      <footer className="footer">
        <span>Rewardly</span>
        <span>Rewards made simple.</span>
      </footer>
    </div>
  );
}

export default App;

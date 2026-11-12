from flask import Flask, render_template, request, redirect, url_for, flash
import sqlite3

app = Flask(__name__)
app.secret_key = "inventariowm-secret-key"
DATABASE = "inventario.db"


def get_db_connection():
    conn = sqlite3.connect(DATABASE)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    with sqlite3.connect(DATABASE) as conn:
        conn.execute(
            """
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL DEFAULT 'General',
                stock INTEGER NOT NULL DEFAULT 0,
                minimum_stock INTEGER NOT NULL DEFAULT 5,
                buy_price REAL NOT NULL DEFAULT 0,
                sale_price REAL NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
            """
        )
        conn.execute(
            """
            CREATE TABLE IF NOT EXISTS movements (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                type TEXT NOT NULL CHECK(type IN ('entry', 'exit')),
                quantity INTEGER NOT NULL,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(product_id) REFERENCES products(id)
            )
            """
        )
        conn.commit()


@app.route("/")
def index():
    conn = get_db_connection()
    products = conn.execute(
        "SELECT * FROM products ORDER BY name ASC"
    ).fetchall()
    movements = conn.execute(
        """
        SELECT m.*, p.name AS product_name
        FROM movements m
        JOIN products p ON p.id = m.product_id
        ORDER BY m.created_at DESC
        LIMIT 10
        """
    ).fetchall()

    total_products = len(products)
    total_stock = sum(product["stock"] for product in products)
    low_stock = [product for product in products if product["stock"] <= product["minimum_stock"]]
    total_value = sum(product["stock"] * product["buy_price"] for product in products)

    conn.close()

    return render_template(
        "index.html",
        products=products,
        movements=movements,
        total_products=total_products,
        total_stock=total_stock,
        low_stock=low_stock,
        total_value=total_value,
    )


@app.route("/product", methods=["POST"])
def add_product():
    name = request.form.get("name", "").strip()
    category = request.form.get("category", "General").strip() or "General"
    stock = int(request.form.get("stock", 0) or 0)
    minimum_stock = int(request.form.get("minimum_stock", 5) or 5)
    buy_price = float(request.form.get("buy_price", 0) or 0)
    sale_price = float(request.form.get("sale_price", 0) or 0)

    if not name:
        flash("El nombre del producto es obligatorio.")
        return redirect(url_for("index"))

    conn = get_db_connection()
    conn.execute(
        """
        INSERT INTO products (name, category, stock, minimum_stock, buy_price, sale_price)
        VALUES (?, ?, ?, ?, ?, ?)
        """,
        (name, category, stock, minimum_stock, buy_price, sale_price),
    )
    conn.commit()
    conn.close()

    flash(f"Producto '{name}' registrado correctamente.")
    return redirect(url_for("index"))


@app.route("/movement", methods=["POST"])
def register_movement():
    product_id = int(request.form.get("product_id") or 0)
    movement_type = request.form.get("type")
    quantity = int(request.form.get("quantity") or 0)
    notes = request.form.get("notes", "").strip()

    if product_id <= 0:
        flash("Debes seleccionar un producto.")
        return redirect(url_for("index"))

    if movement_type not in ("entry", "exit"):
        flash("Tipo de movimiento no válido.")
        return redirect(url_for("index"))

    if quantity <= 0:
        flash("La cantidad debe ser mayor que cero.")
        return redirect(url_for("index"))

    conn = get_db_connection()
    product = conn.execute(
        "SELECT * FROM products WHERE id = ?",
        (product_id,),
    ).fetchone()

    if not product:
        flash("Producto no encontrado.")
        conn.close()
        return redirect(url_for("index"))

    current_stock = product["stock"]
    if movement_type == "exit" and current_stock < quantity:
        flash(f"No hay suficiente stock para '{product['name']}'.")
        conn.close()
        return redirect(url_for("index"))

    new_stock = current_stock + quantity if movement_type == "entry" else current_stock - quantity

    conn.execute(
        "INSERT INTO movements (product_id, type, quantity, notes) VALUES (?, ?, ?, ?)",
        (product_id, movement_type, quantity, notes),
    )
    conn.execute(
        "UPDATE products SET stock = ? WHERE id = ?",
        (new_stock, product_id),
    )
    conn.commit()
    conn.close()

    action = "registrada" if movement_type == "entry" else "realizada"
    flash(f"Movimiento {action} correctamente.")
    return redirect(url_for("index"))


if __name__ == "__main__":
    init_db()
    app.run(debug=True)

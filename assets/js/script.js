document.addEventListener('DOMContentLoaded', function() {
    const cartToggle = document.getElementById("cart-toggle");
    const closeCart = document.getElementById("close-cart");
    const cartSidebar = document.getElementById("cart");
    const cartContainer = document.querySelector('cart-container');
    const logolink = document.getElementById('logo-link');

    const isTouchDevice = () => {
        return window.matchMedia("(hover: none) and (pointer: coarse)").matches;
    };

    const handleCartToggle = () => {
        cartSidebar.classList.toggle("open");
    };

    if (cartToggle && closeCart && cartSidebar) {
        if (isTouchDevice()) {
            cartToggle.addEventListener("click", handleCartToggle);
            closeCart.addEventListener("click", handleCartToggle);

            if (logolink && window.innerWidth <= 768) {
                logolink.addEventListener("click", function(e) {
                    e.preventDefault();
                });
            }
        } else {
            cartContainer.addEventListener('mouseenter', () => cartSidebar.classList.add("open"));
            cartContainer.addEventListener('mouseleave', () => cartSidebar.classList.remove("open"));
        }
    }

        const profileLogo = document.getElementById('profile-logo');
        const profileOptions = document.getElementById('profile-options'); 
        const loginBtn = document.getElementById('login-btn');        
        const logoutBtn = document.getElementById('logout-btn');     
        const usernameDisplay = document.getElementById('username-display'); 
        
        //Lógica para abrir/cerrar el menú desplegable
        if (profileLogo && profileOptions) {
            profileLogo.addEventListener('click', function(event) {
                event.preventDefault();
                profileOptions.classList.toggle('show');
            });
    
            document.addEventListener('click', function(event) {
                if (profileOptions.classList.contains('show') &&
                    !profileLogo.contains(event.target) &&
                    !profileOptions.contains(event.target)) {
                    profileOptions.classList.remove('show');
                }
            });
        }
    
       
        function updateUIForLoggedInUser(userData) {
            if (loginBtn) loginBtn.style.display = 'none';
            if (logoutBtn) logoutBtn.style.display = 'block'; 
            if (usernameDisplay) {
                usernameDisplay.textContent = `Hola, ${userData.name || 'Usuario'}`;
                usernameDisplay.style.display = 'block'; 
            }
            if (profileOptions) profileOptions.classList.remove('show');
        }
    
        function updateUIForLoggedOutUser() {
            if (loginBtn) loginBtn.style.display = 'block'; 
            if (logoutBtn) logoutBtn.style.display = 'none';
            if (usernameDisplay) usernameDisplay.style.display = 'none';
            if (profileOptions) profileOptions.classList.remove('show'); 
        }
    
        async function checkLoginStatus() {
            try {

                const response = await fetch('/proyecto_restaurante/php/check_status.php'); 
                if (!response.ok) {
                    console.error("Error del servidor al verificar sesión:", response.status);
                    updateUIForLoggedOutUser(); 
                    return;
                }
                const data = await response.json();
    
                if (data.loggedIn && data.user) {
                    updateUIForLoggedInUser(data.user);
                } else {
                    updateUIForLoggedOutUser();
                }
            } catch (error) {
                console.error("Error al ejecutar checkLoginStatus:", error);
                updateUIForLoggedOutUser(); 
            }
        }
    
       
        if (loginBtn) {
            loginBtn.addEventListener('click', function() {
                window.location.href = '/proyecto_restaurante/php/login.php'; 
            });
        }
    
     
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function() {
                try {
                  
                    const response = await fetch('/proyecto_restaurante/php/logout.php', { method: 'POST' });
                    const data = await response.json(); 
    
                    if (data.loggedOut || response.ok) {
                        updateUIForLoggedOutUser();
                        window.location.href = '/proyecto_restaurante/php/login.php'; 
                    } else {
                        console.error("Respuesta de logout no fue exitosa:", data);
                        alert("Hubo un problema al cerrar sesión. Intenta de nuevo.");
                    }
                } catch (error) {
                    console.error("Error al ejecutar logout:", error);
                    alert("Error de red al cerrar sesión. Intenta de nuevo.");
                }
            });
        }
    
        checkLoginStatus();
     
    });

    const products = [
        { id: 1, name: "Pizza Margarita", price: 30000, img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTY7RbPLpeQGEKr-JMLB6L9kRdCTMtUbFJfJw&s" },
        { id: 2, name: "Hamburguesa Clásica", price: 15000, img: "https://imag.bonviveur.com/hamburguesa-clasica.jpg" },
        { id: 3, name: "Pasta Alfredo", price: 27500, img: "https://www.recetasnestle.com.mx/sites/default/files/srh_recipes/7e918da02773a54eea60d7bf336d6bb6.jpg" },
        { id: 4, name: "Pizza Hawaiana", price: 30000, img: "https://thumbs.dreamstime.com/b/pizza-hawaiana-49611709.jpg" },
        { id: 5, name: "Spaghetti Boloñesa", price: 28000, img: "https://www.laespanolaaceites.com/wp-content/uploads/2019/05/espaguetis-a-la-bolonesa-1080x671.jpg" },
        { id: 6, name: "Ensalada César", price: 22000, img: "https://sarasellos.com/wp-content/uploads/2024/07/ensalada-cesar1.jpg" },
        { id: 7, name: "Sushi", price: 40000, img: "https://cloudfront-us-east-1.images.arcpublishing.com/elespectador/JLYGWDUSXFDI7ITQECOXNAG674.jpg" },
        { id: 8, name: "Tacos al pastor", price: 27000, img: "https://comedera.com/wp-content/uploads/sites/9/2017/08/tacos-al-pastor-receta.jpg" },
        { id: 9, name: "Sandwich Club", price: 24000, img: "https://okdiario.com/img/2021/07/30/sandwich-club.jpg" },
        { id: 10, name: "Pollo a la Parrilla", price: 35000, img: "https://diabetesfoodhub.org/sites/foodhub/files/styles/recipe_hero_banner_720w/public/1948-honey-lime-chicken-DFH-MJ20.jpg?h=48784f2c&itok=U7I-JyOA" },
        { id: 11, name: "Arepas Rellenas", price: 20000, img: "https://i.pinimg.com/736x/ac/91/01/ac91013c868cd74b19b13db48e951386.jpg" },
        { id: 12, name: "Lasaña de Carne", price: 32000, img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTRjCnEj8ga3_zb4XhCuNyXm9oke8Q2ZDSGQA&s" }
    ];

    let cart = [];

    const productListContainer = document.getElementById("product-list");
    const cartItemsList = document.getElementById("cart-items");
    const cartTotalElement = document.getElementById("cart-total");
    const clearCartButton = document.getElementById("clear-cart");
    const cartCountSpan = document.getElementById("cart-count");

    function renderProducts() {
        if (!productListContainer) return;

        productListContainer.innerHTML = "";

        products.forEach(product => {
            const productCard = document.createElement("div");
            productCard.className = "product-card";
            productCard.innerHTML = `
                <img src="${product.img}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p class="price">$${product.price.toLocaleString()}</p>
                <button class="add-to-cart-btn" data-id="${product.id}">Agregar</button>
            `;
            productListContainer.appendChild(productCard);
        });
        addEventListenersToButtons();
    }

    function addEventListenersToButtons() {
        const addToCartButtons = document.querySelectorAll(".add-to-cart-btn");
        addToCartButtons.forEach(button => {
            button.addEventListener("click", function() {
                const productId = parseInt(this.dataset.id);
                addToCart(productId, this);
            });
        });
    }

    function addToCart(id, button = null) {
        let product = products.find(p => p.id === id);

        if (!product && button) {
            const specialItem = button.closest(".special-item");
            if (specialItem) {
                const name = specialItem.querySelector("h3")?.innerText || "Producto Especial";
                let priceText = specialItem.querySelector(".price")?.innerText || "0";
                priceText = priceText.replace(/[$.]/g, "");
                const price = parseFloat(priceText) || 0;
                const img = specialItem.querySelector("img")?.src || "";

                product = { id, name, price, img };
            }
        }

        if (product) {
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ ...product, quantity: 1 });
            }
            updateCart();
        }
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
    }

    function renderCartItems() {
        if (!cartItemsList) return;

        cartItemsList.innerHTML = "";

        cart.forEach(item => {
            const listItem = document.createElement("li");
            listItem.innerHTML = `
                <span class="item-name">${item.name} (x${item.quantity})</span>
                <span class="item-price">$${(item.price * item.quantity).toLocaleString()}</span>
                <button class="remove-item-btn" data-id="${item.id}">Eliminar</button>
            `;
            cartItemsList.appendChild(listItem);
        });

        const removeItemButtons = document.querySelectorAll("#cart-items .remove-item-btn");
        removeItemButtons.forEach(button => {
            button.addEventListener("click", function() {
                const itemId = parseInt(this.dataset.id);
                removeFromCart(itemId);
            });
        });
    }

    function updateCartTotal() {
        if (!cartTotalElement) return;

        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        cartTotalElement.innerText = `$${total.toLocaleString()}`;
    }

    function updateCartCount() {
        if (!cartCountSpan) return;

        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCountSpan.innerText = count;
    }

    function updateCart() {
        renderCartItems();
        updateCartTotal();
        updateCartCount();
    }

    if (clearCartButton) {
        clearCartButton.addEventListener('click', () => {
            cart = [];
            updateCart();
        });
    };
    
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.getElementById('nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }

    
    document.getElementById("checkout-button").addEventListener("click", async function() { 
        let cartTotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        if (cartTotal <= 0) {
            alert("El carrito está vacío. Por favor, agrega productos antes de realizar el pedido.");
            return;
        }


        try {
            const response = await fetch('/proyecto_restaurante/php/check_status.php');
            if (!response.ok) {
                console.error("Error del servidor al verificar sesión:", response.status);
                alert("Error al verificar tu sesión. Por favor, intenta iniciar sesión de nuevo.");
                window.location.href = '/proyecto_restaurante/php/login.php'; 
                return;
            }
            const authData = await response.json(); 

            if (!authData.loggedIn || !authData.user || !authData.user.id) {
                
                alert("Debes iniciar sesión para realizar un pedido.");
                window.location.href = '/proyecto_restaurante/php/login.php'; 
                return; // Detener el proceso de checkout
            }

            // Si el usuario SÍ está logueado, obtenemos su ID
            const userId = authData.user.id;
            console.log("Usuario logueado con ID:", userId, "procediendo con el pedido.");

            let cartItems = cart.map(item => ({
                productName: item.name,
                quantity: item.quantity,
                price: item.price
            }));

            let orderData = {
                userId: userId,
                cartItems: cartItems,
                cartTotal: cartTotal 
            };

            console.log("Datos del pedido a enviar:", orderData);

            fetch("/proyecto_restaurante/php/process_order.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    
                },
                body: JSON.stringify(orderData),
            })
            .then(response => {
                 if (!response.ok) {
                     if (response.status === 401) { 
                        
                          alert("Tu sesión ha expirado. Por favor, inicia sesión nuevamente.");
                          window.location.href = '/proyecto_restaurante/php/login.php';
                     } else {
                          alert("Error al procesar el pedido. Intenta nuevamente.");
                     }
                     console.error("Error en la respuesta del servidor:", response.status, response.statusText);
                     throw new Error('Error al procesar el pedido en el servidor.');
                 }
                 return response.json();
            })
            .then(data => {
                if (data.orderNumber && data.cartTotal !== undefined) {
                    alert(`¡Gracias por tu compra! Tu número de pedido es: ${data.orderNumber}`);
                    alert(`Total: $${data.cartTotal.toLocaleString()}`);
                    alert(`Estado del pedido: ${data.status}`);
                } else {
                    alert("Hubo un problema al procesar el pedido. Intenta nuevamente.");
                    console.error("Datos del servidor incompletos o incorrectos:", data);
                }

                
                if (data.orderNumber) {
                    cart = [];
                    updateCart();
                    ;
                }

            })
            .catch(error => {
                console.error("Error al procesar el pedido (fetch):", error);
                alert("Hubo un problema al realizar el pedido. Intenta nuevamente.");
            });

        } catch (error) {
             console.error("Error general en el manejo del checkout:", error);
             alert("Ocurrió un error inesperado. Intenta de nuevo.");
        }
    });

    renderProducts();
    updateCart();

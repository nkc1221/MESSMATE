/* menu.js - Dynamic menu loading from database */

let MENU = {};

// Fetch menu from database
async function loadMenuFromDatabase() {
    try {
        const response = await fetch('get_menu.php');
        MENU = await response.json();
        buildMenu();
    } catch (error) {
        console.error('Error loading menu:', error);
        // Fallback to hardcoded menu if database fails
        loadFallbackMenu();
    }
}

// Fallback menu (your existing hardcoded menu)
function loadFallbackMenu() {
    MENU = {
        Monday: {
            Breakfast:{items:['Chole Bhature / Puri','Red Tea','Amul milk (tea)'], special:false},
            Lunch:{items:['Rice','Roti','Moong dal','Aloo(30%)','Karela(70%)'], special:false},
            Dinner:{items:['Rice','Roti','Masoor dal','Egg masala','Veg kofta'], special:true}
        },
        // ... rest of your existing menu ...
    };
    buildMenu();
}

function buildMenu(){
    const grid = document.getElementById('menuGrid');
    if (!grid) return;
    
    grid.innerHTML = '';

    for (const day in MENU){
        const container = document.createElement('div');
        container.className = 'day-card';

        const h = document.createElement('h3');
        h.textContent = day;
        container.appendChild(h);

        for (const time in MENU[day]){
            const m = MENU[day][time];

            const div = document.createElement('div');
            div.className = 'meal';

            const title = document.createElement('div');
            title.innerHTML = `<strong>${time}</strong>${m.special ? ' <span class="badge">Special</span>' : ''}`;
            div.appendChild(title);

            const ul = document.createElement('ul');
            m.items.forEach(it => {
                const li = document.createElement('li');
                li.textContent = it;
                ul.appendChild(li);
            });
            div.appendChild(ul);

            const actions = document.createElement('div');
            actions.style.marginTop = '8px';

            const like = document.createElement('button');
            like.className = 'ghost';
            like.textContent = '👍';
            like.onclick = () => {
                like.textContent = '👍 Thanks!';
                setTimeout(() => like.textContent = '👍', 900);
            };

            const dislike = document.createElement('button');
            dislike.className = 'ghost';
            dislike.style.marginLeft = '8px';
            dislike.textContent = '👎';
            dislike.onclick = () => {
                dislike.textContent = '👎 Noted';
                setTimeout(() => dislike.textContent = '👎', 900);
            };

            actions.appendChild(like);
            actions.appendChild(dislike);
            div.appendChild(actions);

            container.appendChild(div);
        }

        grid.appendChild(container);
    }
}

// Load menu when page loads
document.addEventListener('DOMContentLoaded', loadMenuFromDatabase);

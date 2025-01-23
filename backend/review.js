document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('park-form');
    const results = document.getElementById('results');

    async function loadParks() {
        const response = await fetch('/backend/load_parks.php');
        const parks = await response.json();
        const parkSelect = document.getElementById('park_id');
        parks.forEach(park => {
            const option = document.createElement('option');
            option.value = park.global_id;
            option.textContent = park.Location;
            parkSelect.appendChild(option);
        });
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault(); 
        const parkId = document.getElementById('park_id').value;

        if (!parkId) {
            results.innerHTML = '<p class="no-selection">Выберите площадку.</p>';
            return;
        }

        const response = await fetch(`/backend/reviews_logic.php?park_id=${parkId}`);
        const data = await response.json();

        results.innerHTML = '';
        if (data.park) {
            results.innerHTML += `
                <div class="park-info styled-container">
                    <h2>Информация о площадке</h2>
                    <p><strong>Адрес:</strong> ${data.park.Location}</p>
                    <p><strong>Площадь:</strong> ${data.park.DogParkArea} м²</p>
                    <p><strong>Элементы:</strong> ${data.park.Elements}</p>
                    
                </div>
            `;
        } else {
            results.innerHTML += '<p class="no-info">Информация о выбранной площадке не найдена.</p>';
        }

        if (data.reviews && data.reviews.length > 0) {
            const reviewsHtml = `
                <div class="reviews styled-container">
                    <h2>Отзывы</h2>
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Оценка</th>
                                <th>Отзыв</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.reviews.map(review => `
                                <tr>
                                    <td>${review.rating}</td>
                                    <td>${review.review_text}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
            results.innerHTML += reviewsHtml;
        } else {
            results.innerHTML += '<p class="no-reviews">Отзывов нет.</p>';
        }
    });

    loadParks(); // Подгружаем список парков при загрузке страницы
});

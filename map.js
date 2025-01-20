ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map", {
        center: [55.8, 37.66],
        zoom: 10
    });

    // Загрузка всех площадок
    fetch('dog_parks.php')
        .then(response => response.json())
        .then(parks => {
            console.log('Загруженные площадки:', parks);
            parks.forEach(park => {
                const placemark = new ymaps.Placemark(
                    [park.latitude, park.longitude], 
                    {}, 
                    {
                        iconLayout: 'default#image',
                        iconImageHref: '/images/island.png',

                        iconImageSize: [40, 40],
                        iconImageOffset: [-15, -42]
                    }
                );
                
                placemark.events.add('click', () => {
                    // При клике на маркер показываем информацию о площадке
                    loadParkDetails(park.global_id, park.latitude, park.longitude);
                });
                myMap.geoObjects.add(placemark);
            });
        })
        .catch(error => console.error('Ошибка загрузки площадок:', error));

    // // Функция для загрузки информации о площадке
    // function loadParkDetails(parkId, latitude, longitude) {
    //     fetch(`get_park_details.php?id=${parkId}`)
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.error) {
    //                 alert(`Ошибка: ${data.error}`);
    //                 return;
    //             }

    //             // Обработка элементов площадки
    //             const elements = data.elements && data.elements.length > 0
    //                 ? data.elements.join(', ')
    //                 : 'Элементы не указаны';

    //             // Отображение информации о площадке
    //             const content = `
    //                 <div>
    //                     <h3>Площадка №${data.id}</h3>
    //                     <p><strong>Адрес:</strong> ${data.address}</p>
    //                     <p><strong>Округ:</strong> ${data.adm_area}, ${data.district}</p>
    //                     <p><strong>Площадь:</strong> ${data.area} м²</p>
    //                     <p><strong>Освещение:</strong> ${data.lighting}</p>
    //                     <p><strong>Ограждение:</strong> ${data.fencing}</p>
    //                     <p><strong>Элементы:</strong> ${elements}</p>
    //                 </div>
                    
    //                 <div id="review-section">
    //                     <h4>Оставить отзыв</h4>
    //                     <form id="review-form" action="submit_review.php" method="POST">
    //                         <label for="dog_type">Тип собаки:</label>
    //                         <select id="dog_type" name="dog_type">
    //                             <option value="small">Маленькая</option>
    //                             <option value="medium">Средняя</option>
    //                             <option value="large">Большая</option>
    //                         </select><br>
                            
    //                         <label for="rating">Оценка (1-5):</label>
    //                         <input type="number" id="rating" name="rating" min="1" max="5" required><br>
                            
    //                         <label for="review_text">Отзыв:</label><br>
    //                         <textarea id="review_text" name="review_text" rows="4" cols="50" required></textarea><br>
                            
    //                         <!-- Скрытые поля -->
    //                         <input type="hidden" id="park_id" name="park_id" value="${parkId}">
    //                         <button type="submit">Отправить отзыв</button>
    //                     </form>
    //                 </div>
    //             `;
    //             myMap.balloon.open([latitude, longitude], content);
    //         })
    //         .catch(error => console.error('Ошибка загрузки данных о площадке:', error));
    // }
    function loadParkDetails(parkId, latitude, longitude) {
    fetch(`get_park_details.php?id=${parkId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(`Ошибка: ${data.error}`);
                return;
            }
            const elements = data.elements && data.elements.length > 0
                ? data.elements.join(', ')
                : 'Элементы не указаны';

            let content = `
                <div>
                    <h3>Площадка №${data.id}</h3>
                    <p><strong>Адрес:</strong> ${data.address}</p>
                    <p><strong>Элементы:</strong> ${elements}</p>
                </div>`;

            // Загрузка отзывов для площадки
            fetch(`get_reviews.php?parkId=${parkId}`)
                .then(response => response.json())
                .then(reviews => {
                    if (reviews && reviews.length > 0) {
                        content += '<h4>Отзывы:</h4><ul>';
                        reviews.forEach(review => {
                            content += `<li>${review.rating} звезд: ${review.comment}</li>`;
                        });
                        content += '</ul>';
                    } else {
                        content += '<p>Отзывов пока нет.</p>';
                    }
                    // Отображаем всю информацию в балуне на карте
                    const balloon = new ymaps.Balloon(myMap);
                    balloon.open([latitude, longitude], content);
                })
                .catch(error => console.error('Ошибка загрузки отзывов:', error));
        })
        .catch(error => console.error('Ошибка загрузки информации о площадке:', error));
}

}

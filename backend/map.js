var myMap; 

function init() {
    myMap = new ymaps.Map("map", {
        center: [55.8, 37.66],
        zoom: 10
    });

    fetchParks();  
}

function fetchParks(dogType = "", ratingRange = "") {
    console.log(`Запрос на сервер с dog_type=${dogType} и rating_range=${ratingRange}`);

    myMap.geoObjects.removeAll();

    fetch(`/backend/filter.php?dog_type=${dogType}&rating_range=${ratingRange}`)
    .then(response => response.json())
    .then(parks => {
        console.log('Получены данные о парках:', parks);

        if (parks.length === 0) {
            console.log('Нет парков, удовлетворяющих фильтрам');
            alert('Нет площадок, соответствующих фильтрам.');
        }

        parks.forEach(park => {
            const placemark = new ymaps.Placemark(
                [park.latitude, park.longitude], 
                { balloonContent: `<strong>Локация:</strong> ${park.Location}` },
                {
                    iconLayout: 'default#image',
                    iconImageHref: '/assets/images/island.png',
                    iconImageSize: [40, 40],
                    iconImageOffset: [-20, -20]
                }
            );

            placemark.events.add('click', () => {
                loadParkDetails(park.global_id, park.latitude, park.longitude);
            });

            myMap.geoObjects.add(placemark); 
        });
    })
    .catch(error => {
        console.error('Ошибка загрузки площадок:', error);
        alert('Ошибка при загрузке данных');
    });
}


function filterParks() {
    const dogType = document.getElementById('dog-type').value;
    const ratingRange = document.getElementById('rating-range').value;

    console.log(`Выбрано: dog_type=${dogType}, rating_range=${ratingRange}`);

    if (!dogType && !ratingRange) {
        console.log("Ошибка: фильтры не выбраны!");
    }

    fetchParks(dogType, ratingRange);
}

function loadParkDetails(parkId, latitude, longitude) {
    fetch(`/backend/get_park_details.php?id=${parkId}`)
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
                    <p><strong>Округ:</strong> ${data.adm_area}, ${data.district}</p>
                    <p><strong>Площадь:</strong> ${data.area} м²</p>
                    <p><strong>Освещение:</strong> ${data.lighting}</p>
                    <p><strong>Ограждение:</strong> ${data.fencing}</p>
                    <p><strong>Элементы:</strong> ${elements}</p>
                </div>
                <div id="review-section">
                    <h4>Оставить отзыв</h4>
                    <form id="review-form" method="POST" onsubmit="handleSubmitReviewForm(event)">
            <label for="dog_type">Тип собаки:</label>
            <select id="dog_type" name="dog_type">
                <option value="Маленькая">Маленькая</option>
                <option value="Средняя">Средняя</option>
                <option value="Большая">Большая</option>
            </select><br>
            <label for="rating">Оценка (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required><br>
            <label for="review_text">Отзыв:</label><br>
            <textarea id="review_text" name="review_text" rows="4" cols="50" required></textarea><br>
            <input type="hidden" id="park_id" name="park_id" value="${parkId}">
            <button type="submit">Отправить отзыв</button></form>
                </div>`;

            fetch(`/backend/get_reviews.php?id=${parkId}`)
                .then(response => response.json())
                .then(reviews => {
                    if (reviews.error) {
                        content += '<p>Ошибка загрузки отзывов.</p>';
                    } else if (reviews.length > 0) {
                        content += '<h4>Отзывы:</h4><ul>';
                        reviews.forEach(review => {
                            content += `<li>${review.dog_type}: ${review.rating} звезд, отзыв: ${review.review_text}</li>`;
                        });
                        content += '</ul>';
                    } else {
                        content += '<p>Отзывов пока нет.</p>';
                    }

                    myMap.balloon.open([latitude, longitude], content);
                })
                .catch(error => console.error('Ошибка загрузки отзывов:', error));

            myMap.balloon.open([latitude, longitude], content);
        })
        .catch(error => console.error('Ошибка загрузки данных о площадке:', error));
    }


    function handleSubmitReviewForm(event) {
        event.preventDefault(); // Предотвращаем стандартное поведение формы
    
        const form = event.target;
        const formData = new FormData(form);
    
        fetch('/backend/submit_review.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Отзыв успешно отправлен!');
                form.reset(); // Сбрасываем форму
            } else {
                alert(`Ошибка: ${result.error}`);
            }
        })
        .catch(error => {
            console.error('Ошибка отправки отзыва:', error);
            alert('Произошла ошибка при отправке отзыва.');
        });
    }
    

ymaps.ready(init);


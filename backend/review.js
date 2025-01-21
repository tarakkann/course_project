document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('review-form');

    form.addEventListener('submitform', function(event) {
        event.preventDefault();  // Это гарантирует, что форма не будет отправляться стандартным способом

        const formData = new FormData(form);  // Собираем данные из формы

        // Отправляем данные на сервер через AJAX
        fetch('/backend/submit_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())  // Ожидаем JSON-ответ от сервера
        .then(data => {
            if (data.error) {
                alert(data.error);  // Показываем ошибку, если что-то пошло не так
            } else {
                alert(data.success);  // Показываем сообщение о том, что отзыв успешно добавлен
                // Дополнительно, можно обновить страницу или модифицировать DOM
            }
        })
        .catch(error => console.error('Ошибка:', error));
    });
});

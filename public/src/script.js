document.addEventListener('DOMContentLoaded', () => {
    const imagePopUp = document.getElementById('imagePopUp');
    const popUpImage = document.getElementById('popUpImage');
    const popUpId = document.getElementById('popUpId');
    const popUpNama = document.getElementById('popUpNama');
    const closePopUp = document.getElementById('closePopUp');

    const viewButtons = document.querySelectorAll('.view-button');

    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            const img = button.dataset.img;
            const nim = button.dataset.nim;
            const nama = button.dataset.nama;

            popUpImage.src = img;
            popUpId.textContent = nim;
            popUpNama.textContent = nama;

            imagePopUp.classList.remove('hidden');

            const content = imagePopUp.querySelector('div');
            setTimeout(() => {
                content.classList.remove('opacity-0', 'scale-95');
                content.classList.add('opacity-100', 'scale-100');  
            }, 200);
        });
    });

    closePopUp.addEventListener('click', () => {
        const content = imagePopUp.querySelector('div');
        content.classList.remove('opacity-100', 'scale-100');
        content.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            imagePopUp.classList.add('hidden');
        }, 250);
    });

    imagePopUp.addEventListener('click', (e) => {
        if (e.target === imagePopUp) {
            closePopUp.click();
        }
    });
});


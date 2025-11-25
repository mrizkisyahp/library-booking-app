// Image Pop-up
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

// Dropdown filters
document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.getElementById('dropdownButton');
    const dropdown = document.getElementById('dropdown');
    const options = dropdown.querySelectorAll('li');
    const hiddenInput = document.getElementById('statusSelected');
    const buttonText = dropdownButton.querySelector('span');

    dropdownButton.addEventListener('click', () => {
        const isOpen = !dropdown.classList.contains('hidden');
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    })

    function openDropdown() {
        dropdown.classList.remove('hidden');
        setTimeout(() => {
            dropdown.classList.remove('opacity-0', 'scale-95');
            dropdown.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeDropdown() {
        dropdown.classList.add('opacity-0', 'scale-95');
        dropdown.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => dropdown.classList.add('hidden'), 150);
    }

    options.forEach(li => {
        li.addEventListener('click', () => {
            const value = li.getAttribute('data-value');

            hiddenInput.value = value;

            buttonText.textContent = li.textContent;

            options.forEach(o => o.classList.remove('bg-gray-300'));
            li.classList.add('bg-gray-300');

            closeDropdown();
        });
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !dropdownButton.contains(e.target)) {
            closeDropdown();
        }
    });
});

// Dropdown filters 2
document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.getElementById('dropdownButton2');
    const dropdown = document.getElementById('dropdown2');
    const options = dropdown.querySelectorAll('li');
    const hiddenInput = document.getElementById('statusSelected2');
    const buttonText = dropdownButton.querySelector('span');

    dropdownButton.addEventListener('click', () => {
        const isOpen = !dropdown.classList.contains('hidden');
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    })

    function openDropdown() {
        dropdown.classList.remove('hidden');
        setTimeout(() => {
            dropdown.classList.remove('opacity-0', 'scale-95');
            dropdown.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeDropdown() {
        dropdown.classList.add('opacity-0', 'scale-95');
        dropdown.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => dropdown.classList.add('hidden'), 150);
    }

    options.forEach(li => {
        li.addEventListener('click', () => {
            const value = li.getAttribute('data-value');

            hiddenInput.value = value;

            buttonText.textContent = li.textContent;

            options.forEach(o => o.classList.remove('bg-gray-300'));
            li.classList.add('bg-gray-300');

            closeDropdown();
        });
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !dropdownButton.contains(e.target)) {
            closeDropdown();
        }
    });
});

// toggle password visibility
function togglePassword(id) {
    const input = document.getElementById(id);
    const eyeOpen = document.getElementById(`eyesopen-${id}`);
    const eyeClosed = document.getElementById(`eyesclosed-${id}`);

    if (input.type === "password") {
      input.type = "text";
      eyeOpen.classList.add("hidden");
      eyeClosed.classList.remove("hidden");
    } else {
      input.type = "password";
      eyeOpen.classList.remove("hidden");
      eyeClosed.classList.add("hidden");
    }
  }

  //multi step register form
    function nextStep() {
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
    }

    function prevStep() {
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step1').classList.remove('hidden');
    }
// Image Pop-up
document.addEventListener("DOMContentLoaded", () => {
  const imagePopUp = document.getElementById("imagePopUp");
  const popUpImage = document.getElementById("popUpImage");
  const popUpId = document.getElementById("popUpId");
  const popUpNama = document.getElementById("popUpNama");
  const closePopUp = document.getElementById("closePopUp");

  const viewButtons = document.querySelectorAll(".view-button");

  viewButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const img = button.dataset.img;
      const nim = button.dataset.nim;
      const nama = button.dataset.nama;

      popUpImage.src = img;
      popUpId.textContent = nim;
      popUpNama.textContent = nama;

      imagePopUp.classList.remove("hidden");

      const content = imagePopUp.querySelector("div");
      setTimeout(() => {
        content.classList.remove("opacity-0", "scale-95");
        content.classList.add("opacity-100", "scale-100");
      }, 200);
    });
  });

  closePopUp.addEventListener("click", () => {
    const content = imagePopUp.querySelector("div");
    content.classList.remove("opacity-100", "scale-100");
    content.classList.add("opacity-0", "scale-95");
    setTimeout(() => {
      imagePopUp.classList.add("hidden");
    }, 250);
  });

  imagePopUp.addEventListener("click", (e) => {
    if (e.target === imagePopUp) {
      closePopUp.click();
    }
  });
});

// Dropdown filters
document.addEventListener("DOMContentLoaded", () => {
  const dropdownButton = document.getElementById("dropdownButton");
  const dropdown = document.getElementById("dropdown");
  const options = dropdown.querySelectorAll("li");
  const hiddenInput = document.getElementById("statusSelected");
  const buttonText = dropdownButton.querySelector("span");

  dropdownButton.addEventListener("click", () => {
    const isOpen = !dropdown.classList.contains("hidden");
    if (isOpen) {
      closeDropdown();
    } else {
      openDropdown();
    }
  });

  function openDropdown() {
    dropdown.classList.remove("hidden");
    setTimeout(() => {
      dropdown.classList.remove("opacity-0", "scale-95");
      dropdown.classList.add("opacity-100", "scale-100");
    }, 10);
  }

  function closeDropdown() {
    dropdown.classList.add("opacity-0", "scale-95");
    dropdown.classList.remove("opacity-100", "scale-100");
    setTimeout(() => dropdown.classList.add("hidden"), 150);
  }

  options.forEach((li) => {
    li.addEventListener("click", () => {
      const value = li.getAttribute("data-value");

      hiddenInput.value = value;

      buttonText.textContent = li.textContent;

      options.forEach((o) => o.classList.remove("bg-gray-300"));
      li.classList.add("bg-gray-300");

      closeDropdown();
    });
  });

  document.addEventListener("click", (e) => {
    if (!dropdown.contains(e.target) && !dropdownButton.contains(e.target)) {
      closeDropdown();
    }
  });
});

// Dropdown filters 2
document.addEventListener("DOMContentLoaded", () => {
  const dropdownButton = document.getElementById("dropdownButton2");
  const dropdown = document.getElementById("dropdown2");
  const options = dropdown.querySelectorAll("li");
  const hiddenInput = document.getElementById("statusSelected2");
  const buttonText = dropdownButton.querySelector("span");

  dropdownButton.addEventListener("click", () => {
    const isOpen = !dropdown.classList.contains("hidden");
    if (isOpen) {
      closeDropdown();
    } else {
      openDropdown();
    }
  });

  function openDropdown() {
    dropdown.classList.remove("hidden");
    setTimeout(() => {
      dropdown.classList.remove("opacity-0", "scale-95");
      dropdown.classList.add("opacity-100", "scale-100");
    }, 10);
  }

  function closeDropdown() {
    dropdown.classList.add("opacity-0", "scale-95");
    dropdown.classList.remove("opacity-100", "scale-100");
    setTimeout(() => dropdown.classList.add("hidden"), 150);
  }

  options.forEach((li) => {
    li.addEventListener("click", () => {
      const value = li.getAttribute("data-value");

      hiddenInput.value = value;

      buttonText.textContent = li.textContent;

      options.forEach((o) => o.classList.remove("bg-gray-300"));
      li.classList.add("bg-gray-300");

      closeDropdown();
    });
  });

  document.addEventListener("click", (e) => {
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
async function nextStep() {
  const nimInput = document.getElementById("nim");
  const nipInput = document.getElementById("nip");
  const emailInput = document.getElementById("email");
  const btn = document.querySelector("#step1 button");
  if (btn) btn.disabled = true;

  const isMahasiswa = nimInput !== null;
  const isDosen = nipInput !== null;

  clearError("email");
  if (isMahasiswa) clearError("nim");
  if (isDosen) clearError("nip");

  let url, body;
  if (isMahasiswa) {
    url = "/register/mahasiswa/validate-step1";
    body = { nim: nimInput.value, email: emailInput.value };
  } else if (isDosen) {
    url = "/register/dosen/validate-step1";
    body = { nip: nipInput.value, email: emailInput.value };
  } else {
    document.getElementById("step1").classList.add("hidden");
    document.getElementById("step2").classList.remove("hidden");
    return;
  }

  try {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(body),
    });

    const result = await response.json();

    if (result.valid) {
      document.getElementById("step1").classList.add("hidden");
      document.getElementById("step2").classList.remove("hidden");
    } else if (result.errors) {
      if (result.errors.nim) showError("nim", result.errors.nim[0]);
      if (result.errors.nip) showError("nip", result.errors.nip[0]);
      if (result.errors.email) showError("email", result.errors.email[0]);
    }
  } catch (err) {
    console.error("Validation error:", err);
  } finally {
    if (btn) btn.disabled = false;
  }
}

function showError(fieldId, message) {
  const input = document.getElementById(fieldId);
  if (!input) return;

  input.classList.add("border-red-500");
  input.classList.remove("border-gray-300");

  let errorEl = document.getElementById(fieldId + "_error");
  if (errorEl) errorEl.remove();

  errorEl = document.createElement("p");
  errorEl.id = fieldId + "_error";
  errorEl.className = "mt-1 text-sm text-red-600";
  input.insertAdjacentElement("afterend", errorEl);
  errorEl.textContent = message;
}

function clearError(fieldId) {
  const input = document.getElementById(fieldId);
  if (!input) return;

  input.classList.remove("border-red-500");
  input.classList.add("border-gray-300");

  const errorEl = document.getElementById(fieldId + "_error");
  if (errorEl) errorEl.remove();
}

document.addEventListener("DOMContentLoaded", () => {
  const step1Inputs = document.querySelectorAll("#step1 input");
  step1Inputs.forEach((input) => {
    input.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        nextStep();
      }
    });
  });
});

function prevStep() {
  document.getElementById("step2").classList.add("hidden");
  document.getElementById("step1").classList.remove("hidden");
}

// copy text
function copyToken() {
  const text = document.getElementById("inviteToken").textContent.trim();
  const toast = document.getElementById("copyToast");

  navigator.clipboard.writeText(text).then(() => {
    toast.style.opacity = 1;
    setTimeout(() => (toast.style.opacity = 0), 1500);
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const step1 = document.getElementById("step1");
  const step2 = document.getElementById("step2");

  if (step1 && step2 && step1.dataset.hasStep2Errors === "true") {
    step1.classList.add("hidden");
    step2.classList.remove("hidden");
  }
});

// Verify/index

document.getElementById("resendForm").addEventListener("submit", function (e) {
  const resendBtn = document.getElementById("resendBtn");
  const resendText = document.getElementById("resendText");
  const resendLoading = document.getElementById("resendLoading");

  // Disable button to prevent double submission
  resendBtn.disabled = true;
  resendText.classList.add("hidden");
  resendLoading.classList.remove("hidden");

  // Re-enable after 5 seconds as fallback
  setTimeout(function () {
    resendBtn.disabled = false;
    resendText.classList.remove("hidden");
    resendLoading.classList.add("hidden");
  }, 5000);
});

// ResetPassword/forgot
document.getElementById("forgotForm").addEventListener("submit", function (e) {
  const submitBtn = document.getElementById("submitBtn");
  const btnText = document.getElementById("btnText");
  const btnLoading = document.getElementById("btnLoading");

  // Disable button to prevent double submission
  submitBtn.disabled = true;
  btnText.classList.add("hidden");
  btnLoading.classList.remove("hidden");

  // Re-enable after 5 seconds as fallback (in case of error)
  setTimeout(function () {
    submitBtn.disabled = false;
    btnText.classList.remove("hidden");
    btnLoading.classList.add("hidden");
  }, 5000);
});

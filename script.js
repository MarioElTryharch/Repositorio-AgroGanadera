// =============================================
// MÓDULO PRINCIPAL - CONTROL DE EVENTOS DOM
// =============================================
document.addEventListener("DOMContentLoaded", function () {
  console.log("Página cargada - Inicializando componentes");

  // Inicializar componentes comunes
  initMobileMenu();
  initCarousel();
  initLoginForm();
  initRegisterForm();
  initAgricultivoModals();

  // Detectar página específica y inicializar componentes correspondientes
  if (document.querySelector(".inventario-container")) {
    console.log("Página AgroInventario detectada");
    initInventarioPage();
  }

  if (document.querySelector(".empleados-container")) {
    console.log("Página AgroEmpleados detectada");
    initEmpleadosPage();
  }

  if (
    document.querySelector(".hacienda-container") ||
    document.querySelector(".bufalino-container") ||
    document.querySelector(".agricultivo-container")
  ) {
    console.log("Página con acordeones detectada");
    initAccordions();
  }
});

// =============================================
// INICIALIZACIÓN DE PÁGINA INVENTARIO
// =============================================
function initInventarioPage() {
  console.log("Inicializando página de inventario");

  // Inicializar acordeones
  initAccordions();

  // Configurar modales específicos de inventario
  setupModal("modalNuevoItem", "#btnNuevoItem", "formNuevoItem");
  setupModal("modalNuevaCategoria", "#btnNuevaCategoria", "formNuevaCategoria");
  setupModal(
    "modalAjusteInventario",
    "#btnAjusteInventario",
    "formAjusteInventario"
  );
  setupModal(
    "modalConfigurarAlertas",
    "#btnConfigurarAlertas",
    "formConfigurarAlertas"
  );

  // Función para exportar inventario
  const btnReporte = document.getElementById("btnReporteInventario");
  if (btnReporte) {
    btnReporte.addEventListener("click", function () {
      console.log("Generando reporte de inventario...");
      showAlert("Reporte de inventario generado correctamente", "success");

      // Simular descarga
      setTimeout(() => {
        const link = document.createElement("a");
        link.href = "#";
        link.download = "reporte_inventario.xlsx";
        link.click();
      }, 1000);
    });
  }

  // Función para buscar ítems
  const buscarItem = document.querySelector("#buscarItem");
  if (buscarItem) {
    buscarItem.addEventListener("input", function (e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll(".data-table tbody tr");

      rows.forEach((row) => {
        const itemName = row
          .querySelector("td:nth-child(2)")
          .textContent.toLowerCase();
        row.style.display = itemName.includes(searchTerm) ? "" : "none";
      });
    });
  }

  // Filtrar por categoría
  const filtroCategoria = document.getElementById("filtroCategoria");
  if (filtroCategoria) {
    filtroCategoria.addEventListener("change", function () {
      const category = this.value;
      const rows = document.querySelectorAll(".data-table tbody tr");

      rows.forEach((row) => {
        const itemCategory = row
          .querySelector("td:nth-child(3)")
          .textContent.toLowerCase();
        if (!category || itemCategory.includes(category.toLowerCase())) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  }
}

// =============================================
// INICIALIZACIÓN DE PÁGINA EMPLEADOS
// =============================================
function initEmpleadosPage() {
  console.log("Inicializando página de empleados");

  // Inicializar acordeones
  initAccordions();

  // Configurar modales específicos de empleados
  setupModal("modalNuevoEmpleado", "#btnNuevoEmpleado", "formNuevoEmpleado");
  setupModal("modalAsignarTurno", "#btnAsignarTurno", "formAsignarTurno");
  setupModal(
    "modalNuevaCapacitacion",
    "#btnNuevaCapacitacion",
    "formNuevaCapacitacion"
  );

  // Inicializar datepickers
  initDatePickers();

  // Configurar búsqueda de empleados
  setupEmployeeSearch();

  // Configurar filtros
  setupFilters();

  // Función para generar reporte PDF
  const btnGenerarPDF = document.getElementById("btnGenerarPDF");
  if (btnGenerarPDF) {
    btnGenerarPDF.addEventListener("click", function () {
      showAlert("Generando reporte en PDF...", "success");

      // Simular generación de PDF
      setTimeout(() => {
        const link = document.createElement("a");
        link.href = "#";
        link.download = "reporte_empleados.pdf";
        link.click();
        showAlert("Reporte PDF generado correctamente", "success");
      }, 2000);
    });
  }
}

// =============================================
// MÓDULO DE ACORDEONES (Reutilizable - CORREGIDO)
// =============================================
function initAccordions() {
  const accordionSections = document.querySelectorAll(".menu-section");
  console.log(`Encontradas ${accordionSections.length} secciones de acordeón`);

  accordionSections.forEach((section, index) => {
    const header = section.querySelector("h2");
    const content = section.querySelector(".menu-content");
    const icon = header?.querySelector("i:last-child");

    if (!header || !content) {
      console.warn("Sección de acordeón sin header o content:", section);
      return;
    }

    // Configurar el estado inicial CORREGIDO
    if (section.classList.contains("active")) {
      content.style.display = "block";
      content.style.maxHeight = content.scrollHeight + "px";
      if (icon) {
        icon.classList.remove("fa-caret-right");
        icon.classList.add("fa-caret-down");
      }
    } else {
      content.style.display = "none";
      content.style.maxHeight = "0";
      if (icon) {
        icon.classList.remove("fa-caret-down");
        icon.classList.add("fa-caret-right");
      }
    }

    // Configurar evento click CORREGIDO
    header.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      console.log("Click en acordeón:", section);

      // Cerrar todas las secciones primero (comportamiento de acordeón)
      accordionSections.forEach((s) => {
        if (s !== section && s.classList.contains("active")) {
          s.classList.remove("active");
          const sContent = s.querySelector(".menu-content");
          const sIcon = s.querySelector("h2 i:last-child");
          if (sContent) {
            sContent.style.display = "none";
            sContent.style.maxHeight = "0";
          }
          if (sIcon) {
            sIcon.classList.remove("fa-caret-down");
            sIcon.classList.add("fa-caret-right");
          }
        }
      });

      // Alternar la sección clickeada
      const isActive = section.classList.contains("active");
      section.classList.toggle("active");

      if (!isActive) {
        // Abrir sección
        content.style.display = "block";
        content.style.maxHeight = content.scrollHeight + "px";
        if (icon) {
          icon.classList.remove("fa-caret-right");
          icon.classList.add("fa-caret-down");
        }
      } else {
        // Cerrar sección
        content.style.display = "none";
        content.style.maxHeight = "0";
        if (icon) {
          icon.classList.remove("fa-caret-down");
          icon.classList.add("fa-caret-right");
        }
      }
    });
  });

  // Activar la primera sección por defecto si existe y ninguna está activa
  const firstSection = document.querySelector(".menu-section");
  const activeSection = document.querySelector(".menu-section.active");

  if (firstSection && !activeSection) {
    firstSection.classList.add("active");
    const firstContent = firstSection.querySelector(".menu-content");
    const firstIcon = firstSection.querySelector("h2 i:last-child");
    if (firstContent) {
      firstContent.style.display = "block";
      firstContent.style.maxHeight = firstContent.scrollHeight + "px";
    }
    if (firstIcon) {
      firstIcon.classList.remove("fa-caret-right");
      firstIcon.classList.add("fa-caret-down");
    }
  }
}

// =============================================
// FUNCIONES UTILITARIAS PARA EMPLEADOS
// =============================================

// Función para inicializar datepickers
function initDatePickers() {
  document.querySelectorAll('input[type="date"]').forEach((input) => {
    if (!input.value) {
      const today = new Date().toISOString().split("T")[0];
      input.value = today;
    }
  });
}

// Función para configurar búsqueda de empleados
function setupEmployeeSearch() {
  const searchInput = document.querySelector("#buscarEmpleado");
  if (!searchInput) return;

  searchInput.addEventListener("input", function (e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll(".data-table tbody tr");

    rows.forEach((row) => {
      const employeeName = row
        .querySelector("td:nth-child(2)")
        .textContent.toLowerCase();
      const employeeId = row
        .querySelector("td:nth-child(1)")
        .textContent.toLowerCase();

      if (
        employeeName.includes(searchTerm) ||
        employeeId.includes(searchTerm)
      ) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
}

// Función para configurar filtros
function setupFilters() {
  // Filtro por departamento
  const filterDepto = document.querySelector("#filtroDepartamento");
  if (filterDepto) {
    filterDepto.addEventListener("change", function () {
      const depto = this.value;
      filterTableByColumn(3, depto);
    });
  }

  // Filtro por cargo
  const filterCargo = document.querySelector("#filtroCargo");
  if (filterCargo) {
    filterCargo.addEventListener("change", function () {
      const cargo = this.value;
      filterTableByColumn(4, cargo);
    });
  }

  // Filtro por estatus
  const filterStatus = document.querySelector("#filtroEstatus");
  if (filterStatus) {
    filterStatus.addEventListener("change", function () {
      const status = this.value;
      filterTableByColumn(5, status);
    });
  }
}

// Función para filtrar tabla por columna
function filterTableByColumn(columnIndex, filterValue) {
  const rows = document.querySelectorAll(".data-table tbody tr");

  rows.forEach((row) => {
    const cellValue = row
      .querySelector(`td:nth-child(${columnIndex})`)
      .textContent.toLowerCase();

    if (!filterValue || cellValue.includes(filterValue.toLowerCase())) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// =============================================
// FUNCIONES UTILITARIAS (Reutilizables)
// =============================================

// Función para configurar modales genéricos
function setupModal(modalId, openButtonSelector, formId) {
  const modal = document.getElementById(modalId);
  if (!modal) {
    console.warn(`Modal no encontrado: ${modalId}`);
    return;
  }

  // Abrir modal
  const openButtons = document.querySelectorAll(openButtonSelector);
  openButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      modal.style.display = "flex";
      document.body.style.overflow = "hidden";
    });
  });

  // Cerrar modal
  const closeButtons = modal.querySelectorAll(".close-modal, .btn-cancel");
  closeButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      modal.style.display = "none";
      document.body.style.overflow = "auto";
    });
  });

  // Cerrar al hacer clic fuera
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  });

  // Manejar envío de formulario si existe
  const form = document.getElementById(formId);
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (validateForm(form)) {
        showAlert("Registro guardado correctamente", "success");
        setTimeout(() => {
          modal.style.display = "none";
          document.body.style.overflow = "auto";
          form.reset();
        }, 1500);
      }
    });
  }
}

// Resto de las funciones utilitarias permanecen igual...
function initMobileMenu() {
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const mainNav = document.querySelector(".main-nav");

  if (mobileMenuToggle && mainNav) {
    mobileMenuToggle.addEventListener("click", function () {
      mainNav.classList.toggle("active");
      this.innerHTML = mainNav.classList.contains("active")
        ? '<i class="fas fa-times"></i>'
        : '<i class="fas fa-bars"></i>';
    });

    const navLinks = document.querySelectorAll(".main-nav a");
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        if (window.innerWidth <= 768) {
          mainNav.classList.remove("active");
          mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        }
      });
    });
  }
}

function initCarousel() {
  const carousel = document.querySelector(".carousel");
  if (!carousel) return;

  const inner = carousel.querySelector(".carousel-inner");
  const items = carousel.querySelectorAll(".carousel-item");
  const prevBtn = carousel.querySelector(".prev");
  const nextBtn = carousel.querySelector(".next");
  const indicatorsContainer = carousel.querySelector(".carousel-indicators");

  let currentIndex = 0;
  let intervalId;
  const intervalTime = 5000;

  if (indicatorsContainer && indicatorsContainer.children.length === 0) {
    items.forEach((_, index) => {
      const indicator = document.createElement("span");
      indicator.addEventListener("click", () => goToSlide(index));
      indicatorsContainer.appendChild(indicator);
    });
  }

  const indicators = indicatorsContainer.querySelectorAll("span");

  function updateCarousel() {
    inner.style.transform = `translateX(-${currentIndex * 100}%)`;

    items.forEach((item) => item.classList.remove("active"));
    items[currentIndex].classList.add("active");

    indicators.forEach((indicator, index) => {
      indicator.classList.toggle("active", index === currentIndex);
    });
  }

  function goToSlide(index) {
    currentIndex = (index + items.length) % items.length;
    updateCarousel();
    resetInterval();
  }

  function nextSlide() {
    goToSlide(currentIndex + 1);
  }

  function prevSlide() {
    goToSlide(currentIndex - 1);
  }

  function startInterval() {
    intervalId = setInterval(nextSlide, intervalTime);
  }

  function resetInterval() {
    clearInterval(intervalId);
    startInterval();
  }

  if (nextBtn)
    nextBtn.addEventListener("click", () => {
      nextSlide();
      resetInterval();
    });

  if (prevBtn)
    prevBtn.addEventListener("click", () => {
      prevSlide();
      resetInterval();
    });

  let touchStartX = 0;
  let touchEndX = 0;

  carousel.addEventListener(
    "touchstart",
    (e) => {
      touchStartX = e.changedTouches[0].screenX;
    },
    { passive: true }
  );

  carousel.addEventListener(
    "touchend",
    (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    },
    { passive: true }
  );

  function handleSwipe() {
    if (touchEndX < touchStartX - 50) {
      nextSlide();
    } else if (touchEndX > touchStartX + 50) {
      prevSlide();
    }
    resetInterval();
  }

  updateCarousel();
  startInterval();

  carousel.addEventListener("mouseenter", () => clearInterval(intervalId));
  carousel.addEventListener("mouseleave", startInterval);
}

function initLoginForm() {
  const loginForm = document.getElementById("loginForm");
  if (!loginForm) return;

  // ... (código existente para toggle password)

  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!email || !password) {
      showAlert("Por favor completa todos los campos", "error");
      return;
    }

    if (!validateEmail(email)) {
      showAlert("Por favor ingresa un email válido", "error");
      return;
    }

    // Enviar datos al servidor
    const formData = new FormData();
    formData.append("email", email);
    formData.append("password", password);

    fetch("api/login.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showAlert(data.message, "success");
          setTimeout(() => {
            window.location.href = "index.php";
          }, 1500);
        } else {
          showAlert(data.message, "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showAlert("Error al iniciar sesión", "error");
      });
  });
}

function initRegisterForm() {
  const registerForm = document.getElementById("registerForm");
  if (!registerForm) return;

  // ... (código existente para toggle password y validaciones)

  registerForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const firstName = document.getElementById("firstName").value.trim();
    const lastName = document.getElementById("lastName").value.trim();
    const email = document.getElementById("email").value.trim();
    const userType = document.getElementById("userType").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const termsChecked = document.getElementById("terms").checked;

    // Validaciones (mantén las existentes)
    if (
      !firstName ||
      !lastName ||
      !email ||
      !userType ||
      !password ||
      !confirmPassword
    ) {
      showAlert("Por favor completa todos los campos", "error");
      return;
    }

    if (!validateEmail(email)) {
      showAlert("Por favor ingresa un email válido", "error");
      return;
    }

    if (password !== confirmPassword) {
      showAlert("Las contraseñas no coinciden", "error");
      return;
    }

    if (!termsChecked) {
      showAlert("Debes aceptar los términos y condiciones", "error");
      return;
    }

    // Enviar datos al servidor
    const formData = new FormData();
    formData.append("firstName", firstName);
    formData.append("lastName", lastName);
    formData.append("email", email);
    formData.append("userType", userType);
    formData.append("password", password);

    fetch("api/register.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showAlert(data.message, "success");
          setTimeout(() => {
            window.location.href = "login.php";
          }, 1500);
        } else {
          showAlert(data.message, "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showAlert("Error al registrar usuario", "error");
      });
  });
}

function initAgricultivoModals() {
  if (!document.querySelector(".agricultivo-container")) return;

  const modalsConfig = [
    {
      btnId: "btnNuevoCultivo",
      modalId: "modalNuevoCultivo",
      formId: "formNuevoCultivo",
    },
    {
      btnId: "btnNuevaActividad",
      modalId: "modalNuevaActividad",
      formId: "formNuevaActividad",
    },
    {
      btnId: "btnNuevoAnalisis",
      modalId: "modalNuevoAnalisis",
      formId: "formNuevoAnalisis",
    },
    {
      btnId: "btnNuevoRiego",
      modalId: "modalNuevoRiego",
      formId: "formNuevoRiego",
    },
    {
      btnId: "btnNuevoTratamiento",
      modalId: "modalNuevoTratamiento",
      formId: "formNuevoTratamiento",
    },
    {
      btnId: "btnRegistrarCosecha",
      modalId: "modalRegistrarCosecha",
      formId: "formRegistrarCosecha",
    },
    {
      btnId: "btnNuevoInsumo",
      modalId: "modalNuevoInsumo",
      formId: "formNuevoInsumo",
    },
  ];

  modalsConfig.forEach((config) => {
    setupModal(config.modalId, `#${config.btnId}`, config.formId);
  });

  document
    .getElementById("btnReportes")
    ?.addEventListener("click", function () {
      showAlert("Generando reporte agrícola...", "success");
      setTimeout(() => {
        showAlert("Reporte generado correctamente", "success");
      }, 2000);
    });
}

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function calculatePasswordStrength(password) {
  let strength = 0;
  const hasUpperCase = /[A-Z]/.test(password);
  const hasLowerCase = /[a-z]/.test(password);
  const hasNumbers = /\d/.test(password);
  const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
  const isLongEnough = password.length >= 8;

  if (hasUpperCase) strength += 20;
  if (hasLowerCase) strength += 20;
  if (hasNumbers) strength += 20;
  if (hasSpecialChars) strength += 20;
  if (isLongEnough) strength += 20;

  if (password.length === 0) {
    return { percentage: 0, color: "#e74c3c", text: "Débil" };
  } else if (strength < 40) {
    return { percentage: 33, color: "#e74c3c", text: "Débil" };
  } else if (strength < 80) {
    return { percentage: 66, color: "#f39c12", text: "Moderada" };
  } else {
    return { percentage: 100, color: "#2ecc71", text: "Fuerte" };
  }
}

function validateForm(form) {
  let isValid = true;
  const requiredInputs = form.querySelectorAll("[required]");

  requiredInputs.forEach((input) => {
    if (!input.value.trim()) {
      input.classList.add("input-error");
      isValid = false;
    } else {
      input.classList.remove("input-error");
    }
  });

  if (form.id === "formNuevoCultivo") {
    const fechaSiembraInput = form.querySelector("#cultivoFechaSiembra");
    const fechaCosechaInput = form.querySelector("#cultivoFechaCosecha");

    if (
      fechaSiembraInput &&
      fechaCosechaInput &&
      fechaSiembraInput.value &&
      fechaCosechaInput.value
    ) {
      const fechaSiembra = new Date(fechaSiembraInput.value);
      const fechaCosecha = new Date(fechaCosechaInput.value);

      if (fechaCosecha < fechaSiembra) {
        showAlert(
          "La fecha de cosecha no puede ser anterior a la siembra",
          "error"
        );
        isValid = false;
      }
    }
  }

  return isValid;
}

function showAlert(message, type) {
  const oldAlert = document.querySelector(".alert");
  if (oldAlert) oldAlert.remove();

  const alert = document.createElement("div");
  alert.className = `alert alert-${type}`;
  alert.textContent = message;

  const headers = document.querySelectorAll(
    ".login-header, .register-header, .agricultivo-header, .main-header, .inventario-header, .empleados-header"
  );
  const header = headers[0] || document.body;

  header.insertAdjacentElement("afterend", alert);

  setTimeout(() => {
    alert.remove();
  }, 3000);
}

// ... (el resto de tus funciones existentes para modales, etc.)

// =============================================
// FUNCIONES PARA AGROINVENTARIO
// =============================================

document.addEventListener("DOMContentLoaded", function () {
  // Verificar si estamos en la página de AgroInventario
  if (!document.querySelector(".inventario-container")) return;

  console.log("Página AgroInventario cargada - Inicializando funciones");

  // Inicializar acordeón (usar la misma función que en AgroAgricultivo)
  initAccordion();

  // Configurar modales
  setupModal("modalNuevoItem", "#btnNuevoItem", "formNuevoItem");
  setupModal("modalNuevaCategoria", "#btnNuevaCategoria", "formNuevaCategoria");
  setupModal(
    "modalAjusteInventario",
    "#btnAjusteInventario",
    "formAjusteInventario"
  );
  setupModal(
    "modalConfigurarAlertas",
    "#btnConfigurarAlertas",
    "formConfigurarAlertas"
  );

  // Función para exportar inventario
  document
    .getElementById("btnReporteInventario")
    .addEventListener("click", function () {
      console.log("Generando reporte de inventario...");
      showAlert("Reporte de inventario generado correctamente", "success");

      // Simular descarga
      setTimeout(() => {
        const link = document.createElement("a");
        link.href = "#";
        link.download = "reporte_inventario.xlsx";
        link.click();
      }, 1000);
    });

  // Función para mostrar alertas
  function showAlert(message, type) {
    const alert = document.createElement("div");
    alert.className = `alert alert-${type}`;
    alert.textContent = message;

    const header = document.querySelector(".inventario-header");
    if (header) {
      header.insertAdjacentElement("afterend", alert);
      setTimeout(() => alert.remove(), 3000);
    }
  }

  // Función para buscar ítems
  document.querySelector("#buscarItem").addEventListener("input", function (e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll(".data-table tbody tr");

    rows.forEach((row) => {
      const itemName = row
        .querySelector("td:nth-child(2)")
        .textContent.toLowerCase();
      row.style.display = itemName.includes(searchTerm) ? "" : "none";
    });
  });

  // Filtrar por categoría
  document
    .getElementById("filtroCategoria")
    .addEventListener("change", function () {
      const category = this.value;
      const rows = document.querySelectorAll(".data-table tbody tr");

      rows.forEach((row) => {
        const itemCategory = row
          .querySelector("td:nth-child(3)")
          .textContent.toLowerCase();
        if (!category || itemCategory.includes(category.toLowerCase())) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });

  // Función para inicializar el acordeón (reutilizable)
  function initAccordion() {
    const accordionSections = document.querySelectorAll(".menu-section");

    accordionSections.forEach((section) => {
      const header = section.querySelector("h2");
      const content = section.querySelector(".menu-content");
      const icon = header.querySelector("i:last-child");

      // Asegurar que el estado inicial sea correcto
      if (section.classList.contains("active")) {
        content.style.maxHeight = content.scrollHeight + "px";
        icon.classList.replace("fa-caret-right", "fa-caret-down");
      } else {
        content.style.maxHeight = "0";
        icon.classList.replace("fa-caret-down", "fa-caret-right");
      }
    });
  }

  // Función para configurar modales genéricos
  function setupModal(modalId, openButtonId, formId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Abrir modal
    const openButtons = document.querySelectorAll(openButtonId);
    openButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";
      });
    });

    // Cerrar modal
    const closeButtons = modal.querySelectorAll(".close-modal");
    closeButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        modal.style.display = "none";
        document.body.style.overflow = "auto";
      });
    });

    // Cerrar al hacer clic fuera
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.style.display = "none";
        document.body.style.overflow = "auto";
      }
    });

    // Manejar envío de formulario
    const form = document.getElementById(formId);
    if (form) {
      form.addEventListener("submit", (e) => {
        e.preventDefault();

        // Validación básica
        const requiredInputs = form.querySelectorAll("[required]");
        let isValid = true;

        requiredInputs.forEach((input) => {
          if (!input.value.trim()) {
            input.style.borderColor = "red";
            isValid = false;
          } else {
            input.style.borderColor = "";
          }
        });

        if (isValid) {
          showAlert("Registro guardado correctamente", "success");
          setTimeout(() => {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
            form.reset();
            // Aquí iría la lógica para actualizar la interfaz
          }, 1500);
        } else {
          showAlert("Por favor complete todos los campos requeridos", "error");
        }
      });
    }
  }
});

// =============================================
// FUNCIONES PARA AGROEMPLEADOS
// =============================================

document.addEventListener("DOMContentLoaded", function () {
  // Verificar si estamos en la página de AgroEmpleados
  if (!document.querySelector(".empleados-container")) return;

  console.log("Página AgroEmpleados cargada - Inicializando funciones");

  // Inicializar acordeón
  initAccordion();

  // Configurar modales
  setupModal("modalNuevoEmpleado", "#btnNuevoEmpleado", "formNuevoEmpleado");
  setupModal("modalNuevoPago", "#btnNuevoPago", "formNuevoPago");
  setupModal(
    "modalNuevaAsignacion",
    "#btnNuevaAsignacion",
    "formNuevaAsignacion"
  );
  setupModal("modalNuevoPermiso", "#btnNuevoPermiso", "formNuevoPermiso");
  setupModal("modalEvaluacion", "#btnNuevaEvaluacion", "formEvaluacion");
  setupModal(
    "modalReportePersonal",
    "#btnGenerarReporte",
    "formReportePersonal"
  );

  // Inicializar datepickers
  initDatePickers();

  // Configurar búsqueda de empleados
  setupEmployeeSearch();

  // Configurar filtros
  setupFilters();

  // Función para inicializar el acordeón
  function initAccordion() {
    const accordionSections = document.querySelectorAll(".menu-section");

    accordionSections.forEach((section) => {
      const header = section.querySelector("h2");
      const content = section.querySelector(".menu-content");
      const icon = header.querySelector("i:last-child");

      // Asegurar que el estado inicial sea correcto
      if (section.classList.contains("active")) {
        content.style.maxHeight = content.scrollHeight + "px";
        icon.classList.replace("fa-caret-right", "fa-caret-down");
      } else {
        content.style.maxHeight = "0";
        icon.classList.replace("fa-caret-down", "fa-caret-right");
      }
    });
  }

  // Función para configurar modales genéricos
  function setupModal(modalId, openButtonId, formId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Abrir modal
    const openButtons = document.querySelectorAll(openButtonId);
    openButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        modal.style.display = "flex";
        document.body.style.overflow = "hidden";

        // Inicializar componentes específicos si es necesario
        if (modalId === "modalNuevoPago") {
          calculatePayment();
        }
      });
    });

    // Cerrar modal
    const closeButtons = modal.querySelectorAll(".close-modal");
    closeButtons.forEach((btn) => {
      btn.addEventListener("click", () => {
        modal.style.display = "none";
        document.body.style.overflow = "auto";
      });
    });

    // Cerrar al hacer clic fuera
    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.style.display = "none";
        document.body.style.overflow = "auto";
      }
    });

    // Manejar envío de formulario
    const form = document.getElementById(formId);
    if (form) {
      form.addEventListener("submit", (e) => {
        e.preventDefault();

        // Validación básica
        const requiredInputs = form.querySelectorAll("[required]");
        let isValid = true;

        requiredInputs.forEach((input) => {
          if (!input.value.trim()) {
            input.style.borderColor = "red";
            isValid = false;
          } else {
            input.style.borderColor = "";
          }
        });

        if (isValid) {
          showAlert("Registro guardado correctamente", "success");
          setTimeout(() => {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
            form.reset();

            // Actualizar la interfaz según el formulario enviado
            if (formId === "formNuevoEmpleado") {
              addEmployeeToTable(form);
            } else if (formId === "formNuevoPago") {
              updatePaymentsTable(form);
            }
          }, 1500);
        } else {
          showAlert("Por favor complete todos los campos requeridos", "error");
        }
      });
    }
  }

  // Función para inicializar datepickers
  function initDatePickers() {
    // Configurar datepickers para todos los inputs de tipo date
    document.querySelectorAll('input[type="date"]').forEach((input) => {
      // Configuración adicional si se usa una librería como flatpickr
      // flatpickr(input, { dateFormat: "d/m/Y", locale: "es" });

      // Configuración básica para inputs nativos
      if (!input.value) {
        const today = new Date().toISOString().split("T")[0];
        input.value = today;
      }
    });
  }

  // Función para configurar búsqueda de empleados
  function setupEmployeeSearch() {
    const searchInput = document.querySelector("#buscarEmpleado");
    if (!searchInput) return;

    searchInput.addEventListener("input", function (e) {
      const searchTerm = e.target.value.toLowerCase();
      const rows = document.querySelectorAll(".empleados-table tbody tr");

      rows.forEach((row) => {
        const employeeName = row
          .querySelector("td:nth-child(2)")
          .textContent.toLowerCase();
        const employeeId = row
          .querySelector("td:nth-child(1)")
          .textContent.toLowerCase();

        if (
          employeeName.includes(searchTerm) ||
          employeeId.includes(searchTerm)
        ) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  }

  // Función para configurar filtros
  function setupFilters() {
    // Filtro por departamento
    const filterDepto = document.querySelector("#filtroDepartamento");
    if (filterDepto) {
      filterDepto.addEventListener("change", function () {
        const depto = this.value;
        filterTableByColumn(3, depto); // Columna 3 es Departamento
      });
    }

    // Filtro por cargo
    const filterCargo = document.querySelector("#filtroCargo");
    if (filterCargo) {
      filterCargo.addEventListener("change", function () {
        const cargo = this.value;
        filterTableByColumn(4, cargo); // Columna 4 es Cargo
      });
    }

    // Filtro por estatus
    const filterStatus = document.querySelector("#filtroEstatus");
    if (filterStatus) {
      filterStatus.addEventListener("change", function () {
        const status = this.value;
        filterTableByColumn(5, status); // Columna 5 es Estatus
      });
    }
  }

  // Función para filtrar tabla por columna
  function filterTableByColumn(columnIndex, filterValue) {
    const rows = document.querySelectorAll(".empleados-table tbody tr");

    rows.forEach((row) => {
      const cellValue = row
        .querySelector(`td:nth-child(${columnIndex})`)
        .textContent.toLowerCase();

      if (!filterValue || cellValue.includes(filterValue.toLowerCase())) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  }

  // Función para calcular pago
  function calculatePayment() {
    const form = document.getElementById("formNuevoPago");
    if (!form) return;

    const salarioInput = form.querySelector("#pagoSalarioBase");
    const diasInput = form.querySelector("#pagoDiasTrabajados");
    const bonosInput = form.querySelector("#pagoBonos");
    const descuentosInput = form.querySelector("#pagoDescuentos");
    const totalInput = form.querySelector("#pagoTotal");

    [salarioInput, diasInput, bonosInput, descuentosInput].forEach((input) => {
      input.addEventListener("input", updatePaymentTotal);
    });

    function updatePaymentTotal() {
      const salario = parseFloat(salarioInput.value) || 0;
      const dias = parseFloat(diasInput.value) || 0;
      const bonos = parseFloat(bonosInput.value) || 0;
      const descuentos = parseFloat(descuentosInput.value) || 0;

      const salarioProporcional = (salario / 30) * dias;
      const total = salarioProporcional + bonos - descuentos;

      totalInput.value = total.toFixed(2);
    }
  }

  // Función para agregar empleado a la tabla
  function addEmployeeToTable(form) {
    const table = document.querySelector(".empleados-table tbody");
    if (!table) return;

    const formData = new FormData(form);
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
            <td>${formData.get("empleadoCedula")}</td>
            <td>${formData.get("empleadoNombre")} ${formData.get(
      "empleadoApellido"
    )}</td>
            <td>${formatDate(formData.get("empleadoFechaIngreso"))}</td>
            <td>${formData.get("empleadoDepartamento")}</td>
            <td>${formData.get("empleadoCargo")}</td>
            <td><span class="status-badge active">Activo</span></td>
            <td>
                <button class="btn-icon" title="Editar"><i class="fas fa-edit"></i></button>
                <button class="btn-icon" title="Historial"><i class="fas fa-history"></i></button>
            </td>
        `;

    table.appendChild(newRow);
  }

  // Función para actualizar tabla de pagos
  function updatePaymentsTable(form) {
    const table = document.querySelector(".pagos-table tbody");
    if (!table) return;

    const formData = new FormData(form);
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
            <td>${formatDate(formData.get("pagoFecha"))}</td>
            <td>${formData.get("pagoEmpleado")}</td>
            <td>${formData.get("pagoPeriodo")}</td>
            <td>${formData.get("pagoSalarioBase")}</td>
            <td>${formData.get("pagoTotal")}</td>
            <td><span class="status-badge active">Pagado</span></td>
            <td>
                <button class="btn-icon" title="Detalles"><i class="fas fa-eye"></i></button>
                <button class="btn-icon" title="Recibo"><i class="fas fa-file-invoice"></i></button>
            </td>
        `;

    table.appendChild(newRow);
  }

  // Función para formatear fecha
  function formatDate(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("es-VE");
  }

  // Función para mostrar alertas
  function showAlert(message, type) {
    const alert = document.createElement("div");
    alert.className = `alert alert-${type}`;
    alert.textContent = message;

    const header = document.querySelector(".empleados-header");
    if (header) {
      // Eliminar alertas previas
      const oldAlert = document.querySelector(".alert");
      if (oldAlert) oldAlert.remove();

      header.insertAdjacentElement("afterend", alert);
      setTimeout(() => alert.remove(), 3000);
    }
  }

  // Función para generar reporte PDF
  document
    .getElementById("btnGenerarPDF")
    .addEventListener("click", function () {
      showAlert("Generando reporte en PDF...", "success");

      // Simular generación de PDF
      setTimeout(() => {
        const link = document.createElement("a");
        link.href = "#";
        link.download = "reporte_empleados.pdf";
        link.click();
        showAlert("Reporte PDF generado correctamente", "success");
      }, 2000);
    });

  // Función para exportar a Excel
  document
    .getElementById("btnExportarExcel")
    .addEventListener("click", function () {
      showAlert("Exportando datos a Excel...", "success");

      // Simular exportación
      setTimeout(() => {
        const link = document.createElement("a");
        link.href = "#";
        link.download = "empleados.xlsx";
        link.click();
        showAlert("Datos exportados a Excel correctamente", "success");
      }, 2000);
    });

  // Inicializar tooltips
  function initTooltips() {
    const tooltipTriggerList = [].slice.call(
      document.querySelectorAll("[title]")
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  // Llamar a la inicialización de tooltips
  initTooltips();
});

function abrirModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.style.display = "flex";
  document.body.style.overflow = "hidden";

  // Activar la primera pestaña y su contenido al abrir el modal
  const tabContents = modal.querySelectorAll(".tab-content");
  const tabButtons = modal.querySelectorAll(".ficha-tab");
  tabContents.forEach((content, i) => {
    if (i === 0) {
      content.classList.add("active");
    } else {
      content.classList.remove("active");
    }
  });
  tabButtons.forEach((btn, i) => {
    if (i === 0) {
      btn.classList.add("active");
    } else {
      btn.classList.remove("active");
    }
  });
}

// Funciones para abrir y cerrar modales

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function (event) {
  if (event.target.classList.contains("modal")) {
    event.target.style.display = "none";
  }
};

// Cambiar entre pestañas
function cambiarPestaña(evt, seccionId) {
  // Ocultar todos los contenidos de pestañas
  const tabContents = document.getElementsByClassName("tab-content");
  for (let i = 0; i < tabContents.length; i++) {
    tabContents[i].classList.remove("active");
  }

  // Desactivar todas las pestañas
  const tabButtons = document.getElementsByClassName("ficha-tab");
  for (let i = 0; i < tabButtons.length; i++) {
    tabButtons[i].classList.remove("active");
  }

  // Activar la pestaña actual
  document.getElementById(seccionId).classList.add("active");
  evt.currentTarget.classList.add("active");
}

// Previsualizar foto seleccionada
function previsualizarFoto(event) {
  const input = event.target;
  const preview = document.getElementById("fotoPreview");

  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      preview.innerHTML = "";
      const img = document.createElement("img");
      img.src = e.target.result;
      preview.appendChild(img);
    };

    reader.readAsDataURL(input.files[0]);
  }
}

// Guardar foto (simulación)
function guardarFoto() {
  const input = document.getElementById("inputFoto");
  const preview = document.getElementById("fotoPreview");

  if (!input.files || !input.files[0]) {
    alert("Por favor, seleccione una imagen primero.");
    return;
  }

  // Simulamos el guardado de la imagen
  const reader = new FileReader();
  reader.onload = function (e) {
    // Crear miniatura para la lista de imágenes guardadas
    const imagenesGuardadas = document.getElementById("imagenesGuardadas");

    // Si es la primera imagen, eliminar el mensaje de no imágenes
    if (imagenesGuardadas.querySelector("p")) {
      imagenesGuardadas.innerHTML = "";
    }

    const miniaturaDiv = document.createElement("div");
    miniaturaDiv.className = "imagen-miniatura";

    const img = document.createElement("img");
    img.src = e.target.result;

    const botonDescarga = document.createElement("button");
    botonDescarga.className = "descargar-imagen";
    botonDescarga.innerHTML = '<i class="fas fa-download"></i>';
    botonDescarga.onclick = function () {
      descargarImagen(
        e.target.result,
        "animal_" + new Date().getTime() + ".jpg"
      );
    };

    miniaturaDiv.appendChild(img);
    miniaturaDiv.appendChild(botonDescarga);
    imagenesGuardadas.appendChild(miniaturaDiv);

    alert("Imagen guardada correctamente.");

    // Limpiar el input y la previsualización
    input.value = "";
    preview.innerHTML = "<span>Vista previa de la imagen</span>";
  };

  reader.readAsDataURL(input.files[0]);
}

// Descargar imagen
function descargarImagen(dataUrl, nombreArchivo) {
  const enlace = document.createElement("a");
  enlace.href = dataUrl;
  enlace.download = nombreArchivo;
  document.body.appendChild(enlace);
  enlace.click();
  document.body.removeChild(enlace);
}

// Manejar envío de formularios
document
  .getElementById("formDatosBasicos")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Datos básicos guardados correctamente.");
  });

document
  .getElementById("formDatosReproductivos")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Datos reproductivos guardados correctamente.");
  });

document
  .getElementById("formDatosProductivos")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Datos productivos guardados correctamente.");
  });

document
  .getElementById("formDatosGenealogicos")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Datos genealógicos guardados correctamente.");
  });

// Toggle del menú móvil
document
  .querySelector(".mobile-menu-toggle")
  .addEventListener("click", function () {
    document.querySelector(".main-nav ul").classList.toggle("show");
  });

// Cerrar modal al hacer clic en el botón .close-modal
document.querySelectorAll(".close-modal").forEach((btn) => {
  btn.addEventListener("click", function () {
    // Buscar el modal más cercano al botón
    const modal = btn.closest(".modal");
    if (modal) {
      modal.style.display = "none";
      document.body.style.overflow = "auto";
    }
  });
});

// =============================================
//modal de cria
// =============================================

// Funciones para abrir y cerrar modales

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function (event) {
  if (event.target.classList.contains("modal")) {
    event.target.style.display = "none";
    document.body.style.overflow = "auto";
  }
};

// Cambiar entre pestañas - FUNCIÓN CORREGIDA
function cambiarPestania(evt, seccionId) {
  // Ocultar todos los contenidos de pestañas
  const tabContents = document.getElementsByClassName("tab-content");
  for (let i = 0; i < tabContents.length; i++) {
    tabContents[i].classList.remove("active");
  }

  // Desactivar todas las pestañas
  const tabButtons = document.getElementsByClassName("ficha-tab");
  for (let i = 0; i < tabButtons.length; i++) {
    tabButtons[i].classList.remove("active");
  }

  // Activar la pestaña actual
  document.getElementById(seccionId).classList.add("active");
  evt.currentTarget.classList.add("active");
}

// Previsualizar foto seleccionada
function previsualizarFoto(event) {
  const input = event.target;
  const preview = document.getElementById("fotoPreview1");

  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      preview.innerHTML = "";
      const img = document.createElement("img");
      img.src = e.target.result;
      preview.appendChild(img);
    };

    reader.readAsDataURL(input.files[0]);
  }
}

// Guardar foto (simulación)
function guardarFoto() {
  const input = document.getElementById("inputFoto1");
  const preview = document.getElementById("fotoPreview1");

  if (!input.files || !input.files[0]) {
    alert("Por favor, seleccione una imagen primero.");
    return;
  }

  // Simulamos el guardado de la imagen
  const reader = new FileReader();
  reader.onload = function (e) {
    // Crear miniatura para la lista de imágenes guardadas
    const imagenesGuardadas = document.getElementById("imagenesGuardadas1");

    // Si es la primera imagen, eliminar el mensaje de no imágenes
    if (imagenesGuardadas.querySelector("p")) {
      imagenesGuardadas.innerHTML = "";
    }

    const miniaturaDiv = document.createElement("div");
    miniaturaDiv.className = "imagen-miniatura1";

    const img = document.createElement("img");
    img.src = e.target.result;

    const botonDescarga = document.createElement("button");
    botonDescarga.className = "descargar-imagen1";
    botonDescarga.innerHTML = '<i class="fas fa-download"></i>';
    botonDescarga.onclick = function () {
      descargarImagen(
        e.target.result,
        "animal_" + new Date().getTime() + ".jpg"
      );
    };

    miniaturaDiv.appendChild(img);
    miniaturaDiv.appendChild(botonDescarga);
    imagenesGuardadas.appendChild(miniaturaDiv);

    alert("Imagen guardada correctamente.");

    // Limpiar el input y la previsualización
    input.value = "";
    preview.innerHTML = "<span>Vista previa de la imagen</span>";
  };

  reader.readAsDataURL(input.files[0]);
}

// Descargar imagen
function descargarImagen(dataUrl, nombreArchivo) {
  const enlace = document.createElement("a");
  enlace.href = dataUrl;
  enlace.download = nombreArchivo;
  document.body.appendChild(enlace);
  enlace.click();
  document.body.removeChild(enlace);
}

// Manejar envío de formularios
document
  .getElementById("formInfoAnimal1")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Información del animal guardada correctamente.");
  });

document
  .getElementById("formDatosBasicos1")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Datos básicos guardados correctamente.");
  });

document
  .getElementById("formGananciaPeso1")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Datos de ganancia de peso guardados correctamente.");
  });

// Inicializar la primera pestaña como activa
document.addEventListener("DOMContentLoaded", function () {
  // Asegurarse de que solo la primera pestaña esté activa al cargar
  const tabContents = document.getElementsByClassName("tab-content");
  for (let i = 0; i < tabContents.length; i++) {
    if (i === 0) {
      tabContents[i].classList.add("active");
    } else {
      tabContents[i].classList.remove("active");
    }
  }
});

// Previsualizar y guardar múltiples fotos en Animales Eliminados
function previsualizarFotosEliminados(event) {
  const input = event.target;
  const preview = document.getElementById("fotoPreviewEliminados");
  const imagenesGuardadas = document.getElementById(
    "imagenesGuardadasEliminados"
  );
  preview.innerHTML = "";
  imagenesGuardadas.innerHTML = "";

  if (input.files && input.files.length > 0) {
    Array.from(input.files).forEach((file, idx) => {
      const reader = new FileReader();
      reader.onload = function (e) {
        // Previsualización
        const img = document.createElement("img");
        img.src = e.target.result;
        img.style.maxWidth = "100px";
        img.style.margin = "5px";
        preview.appendChild(img);

        // Guardar en la lista
        const miniaturaDiv = document.createElement("div");
        miniaturaDiv.className = "imagen-miniatura1";
        const imgMini = document.createElement("img");
        imgMini.src = e.target.result;
        imgMini.style.maxWidth = "80px";
        imgMini.style.margin = "5px";

        const botonDescarga = document.createElement("button");
        botonDescarga.className = "descargar-imagen1";
        botonDescarga.innerHTML = '<i class="fas fa-download"></i>';
        botonDescarga.onclick = function () {
          descargarImagen(e.target.result, "eliminado_" + (idx + 1) + ".jpg");
        };

        miniaturaDiv.appendChild(imgMini);
        miniaturaDiv.appendChild(botonDescarga);
        imagenesGuardadas.appendChild(miniaturaDiv);
      };
      reader.readAsDataURL(file);
    });
  } else {
    preview.innerHTML = "<span>Vista previa de las imágenes</span>";
    imagenesGuardadas.innerHTML = "<p>No hay imágenes guardadas aún.</p>";
  }
}

// ============================================= Funciones para guardar datos de todos los modales

// ======================================================
// 🐄 GESTIÓN DE VIENTRES - CRUD COMPLETO
// ======================================================

// ----------------------
// 🔸 Abrir y cerrar modales
// ----------------------
function abrirModal(idModal) {
  document.getElementById(idModal).style.display = "block";
}
function cerrarModal(idModal) {
  document.getElementById(idModal).style.display = "none";
}

// ----------------------
// 🔸 Cambiar pestañas dentro del modal
// ----------------------
function cambiarPestaña(event, idPestaña) {
  const botones = document.querySelectorAll(".ficha-tab");
  botones.forEach((b) => b.classList.remove("active"));

  const contenidos = document.querySelectorAll(".tab-content");
  contenidos.forEach((c) => c.classList.remove("active"));

  event.target.classList.add("active");
  document.getElementById(idPestaña).classList.add("active");
}

// ----------------------
// 🔸 PREVISUALIZAR FOTO
// ----------------------
function previsualizarFoto(event) {
  const file = event.target.files[0];
  const preview = document.getElementById("fotoPreview");

  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.innerHTML = `<img src="${e.target.result}" alt="Foto del animal" style="max-width:150px; border-radius:8px;">`;
    };
    reader.readAsDataURL(file);
  } else {
    preview.innerHTML = "<span>Vista previa de la imagen</span>";
  }
}

document.getElementById("btnSeccionVientres").addEventListener("click", () => {
  restaurarContadorVientres();
});

function restaurarContadorVientres() {
  const contador = document.getElementById("contadorVientres");
  const contadorGuardado = localStorage.getItem("contadorVientres");

  if (contador && contadorGuardado !== null) {
    contador.textContent = contadorGuardado;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const contador = document.getElementById("contadorVientres");
  const contadorGuardado = localStorage.getItem("contadorVientres");

  if (contador && contadorGuardado !== null) {
    contador.textContent = contadorGuardado;
  }
});

function guardarVientre(
  idFormulario = "formDatosBasicos",
  urlPHP = "api/Agro_Vacuno/Datos/Vientres/vientres_datos.php"
) {
  const form = document.getElementById(idFormulario);
  if (!form) {
    console.error(`❌ El formulario con ID "${idFormulario}" no existe.`);
    return;
  }

  const formData = new FormData(form);

  fetch(urlPHP, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Respuesta del backend:", data);

      if (data.success) {
        alert(data.message || "✅ Guardado correctamente.");

        // 🔹 Actualiza el contador visual y guarda en localStorage
        const contador = document.getElementById("contadorVientres");
        if (contador && data.total !== undefined) {
          const totalVientres = String(data.total);
          contador.textContent = totalVientres;
          localStorage.setItem("contadorVientres", totalVientres); // Guardar persistente
        }

        // 🔹 Cierra el modal
        cerrarModal("modalVientres");
      } else {
        alert("⚠️ Error: " + data.message);
      }
    })
    .catch((err) => {
      console.error("❌ Error al guardar:", err);
      alert("❌ Ocurrió un error al guardar.");
    });
}

function guardarCria() {
  alert("Cria guardada correctamente");
  cerrarModal("modalCria");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarEliminado() {
  alert("Animal eliminado guardado correctamente");
  cerrarModal("modalEliminados");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarToro() {
  alert("Toro guardado correctamente");
  cerrarModal("modalToros");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarInseminador() {
  alert("Inseminador guardado correctamente");
  cerrarModal("modalInseminadores");
  // Aquí iría el código para guardar los datos en el servidor
}

// Función para guardar índices globales
function guardarIndices() {
  alert("Índices globales guardados correctamente");
  cerrarModal("modalIndices");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarServicioCompleto() {
  alert("Servicio guardado correctamente");
  cerrarModal("modalServicios");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarPalpitacion() {
  alert("Palpitación guardada correctamente");
  cerrarModal("modalPalpitaciones");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarParto() {
  alert("Parto guardado correctamente");
  cerrarModal("modalPartos");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarSecado() {
  alert("Secado guardado correctamente");
  cerrarModal("modalSecados");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarProduccionLactea() {
  alert("Producción láctea guardada correctamente");
  cerrarModal("modalProduccionLactea");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarPesoCorporal() {
  alert("Peso corporal guardado correctamente");
  cerrarModal("modalPesoCorporal");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarTratamientoVientres() {
  alert("Tratamiento de vientres guardado correctamente");
  cerrarModal("modalTratamientosVientres");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarTratamientoCrias() {
  alert("Tratamiento de crías guardado correctamente");
  cerrarModal("modalTratamientosCrias");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarNota() {
  alert("Nota guardada correctamente");
  cerrarModal("modalNotas");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarRaza() {
  alert("Raza guardada correctamente");
  cerrarModal("modalRazas");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarCorral() {
  alert("Corral guardado correctamente");
  cerrarModal("modalCorrales");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarLote() {
  alert("Lote guardado correctamente");
  cerrarModal("modalLotes");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarGrupo() {
  alert("Grupo guardado correctamente");
  cerrarModal("modalGrupos");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarColor() {
  alert("Color guardado correctamente");
  cerrarModal("modalColores");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarCruce() {
  alert("Cruce guardado correctamente");
  cerrarModal("modalCruces");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarCategoriaNota() {
  alert("Categoría de nota guardada correctamente");
  cerrarModal("modalCategoriasNotas");
  // Aquí iría el código para guardar los datos en el servidor
}

function guardarDiagnosticoVeterinario() {
  alert("Diagnóstico veterinario guardado correctamente");
  cerrarModal("modalDiagnosticosVeterinarios");
  // Aquí iría el código para guardar los datos en el servidor
}

// ============================================= Funciones para guardar datos de todos los modales

// Función para previsualizar fotos de tratamientos
function previsualizarFotosTratamiento(event) {
  const input = event.target;
  const preview = document.getElementById("fotoPreviewTratamiento");

  if (input.files && input.files.length > 0) {
    preview.innerHTML = "";

    for (let i = 0; i < input.files.length; i++) {
      const reader = new FileReader();

      reader.onload = function (e) {
        const imgContainer = document.createElement("div");
        imgContainer.style.display = "inline-block";
        imgContainer.style.margin = "5px";
        imgContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Evidencia tratamiento ${
          i + 1
        }" style="max-width: 150px; max-height: 150px; border-radius: 4px;">
                    <div style="font-size: 12px; text-align: center;">Evidencia ${
                      i + 1
                    }</div>
                `;
        preview.appendChild(imgContainer);
      };

      reader.readAsDataURL(input.files[i]);
    }
  }
}

// Función para calcular fecha de finalización automáticamente
document.addEventListener("DOMContentLoaded", function () {
  const fechaTratamiento = document.getElementById("fechaTratamiento");
  const numeroAplicaciones = document.getElementById("numeroAplicaciones");
  const frecuenciaAplicacion = document.getElementById("frecuenciaAplicacion");
  const fechaFinalizacion = document.getElementById("fechaFinalizacion");

  function calcularFechaFinalizacion() {
    if (
      fechaTratamiento.value &&
      numeroAplicaciones.value &&
      frecuenciaAplicacion.value
    ) {
      const fechaInicio = new Date(fechaTratamiento.value);
      let diasTratamiento = 0;

      switch (frecuenciaAplicacion.value) {
        case "diaria":
          diasTratamiento = parseInt(numeroAplicaciones.value) - 1;
          break;
        case "12-horas":
          diasTratamiento = Math.ceil(
            (parseInt(numeroAplicaciones.value) - 1) / 2
          );
          break;
        case "48-horas":
          diasTratamiento = (parseInt(numeroAplicaciones.value) - 1) * 2;
          break;
        case "semanal":
          diasTratamiento = (parseInt(numeroAplicaciones.value) - 1) * 7;
          break;
        default:
          diasTratamiento = parseInt(numeroAplicaciones.value) - 1;
      }

      const fechaFin = new Date(fechaInicio);
      fechaFin.setDate(fechaInicio.getDate() + diasTratamiento);
      fechaFinalizacion.value = fechaFin.toISOString().split("T")[0];
    }
  }

  if (fechaTratamiento && numeroAplicaciones && frecuenciaAplicacion) {
    fechaTratamiento.addEventListener("change", calcularFechaFinalizacion);
    numeroAplicaciones.addEventListener("change", calcularFechaFinalizacion);
    frecuenciaAplicacion.addEventListener("change", calcularFechaFinalizacion);
  }
});

// Función para previsualizar fotos de tratamientos de crías
function previsualizarFotosCria(event) {
  const input = event.target;
  const preview = document.getElementById("fotoPreviewCria");

  if (input.files && input.files.length > 0) {
    preview.innerHTML = "";

    for (let i = 0; i < input.files.length; i++) {
      const reader = new FileReader();

      reader.onload = function (e) {
        const imgContainer = document.createElement("div");
        imgContainer.style.display = "inline-block";
        imgContainer.style.margin = "5px";
        imgContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Evidencia tratamiento ${
          i + 1
        }" style="max-width: 150px; max-height: 150px; border-radius: 4px;">
                    <div style="font-size: 12px; text-align: center;">Evidencia ${
                      i + 1
                    }</div>
                `;
        preview.appendChild(imgContainer);
      };

      reader.readAsDataURL(input.files[i]);
    }
  }
}

// Función para calcular automáticamente la edad en días
document.addEventListener("DOMContentLoaded", function () {
  const fechaNacimientoCria = document.getElementById("fechaNacimientoCria");
  const edadDiasCria = document.getElementById("edadDiasCria");

  if (fechaNacimientoCria && edadDiasCria) {
    fechaNacimientoCria.addEventListener("change", function () {
      if (this.value) {
        const fechaNacimiento = new Date(this.value);
        const hoy = new Date();
        const diferenciaTiempo = hoy.getTime() - fechaNacimiento.getTime();
        const diasDiferencia = Math.floor(
          diferenciaTiempo / (1000 * 3600 * 24)
        );
        edadDiasCria.value = Math.max(0, diasDiferencia);
      }
    });
  }
});

// Función para previsualizar fotos de notas
function previsualizarFotosNota(event) {
  const input = event.target;
  const preview = document.getElementById("fotoPreviewNota");

  if (input.files && input.files.length > 0) {
    preview.innerHTML = "";

    for (let i = 0; i < input.files.length; i++) {
      const reader = new FileReader();

      reader.onload = function (e) {
        const imgContainer = document.createElement("div");
        imgContainer.style.display = "inline-block";
        imgContainer.style.margin = "5px";
        imgContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Evidencia ${
          i + 1
        }" style="max-width: 150px; max-height: 150px; border-radius: 4px;">
                    <div style="font-size: 12px; text-align: center;">Evidencia ${
                      i + 1
                    }</div>
                `;
        preview.appendChild(imgContainer);
      };

      reader.readAsDataURL(input.files[i]);
    }
  }
}

// Función para manejar archivos adjuntos
function manejarArchivosNota(event) {
  const input = event.target;
  const listaArchivos = document.getElementById("archivosGuardadosNota");

  if (input.files && input.files.length > 0) {
    listaArchivos.innerHTML = "";

    for (let i = 0; i < input.files.length; i++) {
      const archivo = input.files[i];
      const elementoArchivo = document.createElement("div");
      elementoArchivo.className = "archivo-item";
      elementoArchivo.innerHTML = `
                <i class="fas fa-file"></i> ${archivo.name} (${(
        archivo.size / 1024
      ).toFixed(2)} KB)
            `;
      listaArchivos.appendChild(elementoArchivo);
    }
  }
}

// Inicializar eventos de archivos
document.addEventListener("DOMContentLoaded", function () {
  const documentosInput = document.getElementById("documentosAdjuntos");
  const multimediaInput = document.getElementById("archivosMultimedia");

  if (documentosInput) {
    documentosInput.addEventListener("change", manejarArchivosNota);
  }

  if (multimediaInput) {
    multimediaInput.addEventListener("change", manejarArchivosNota);
  }
});

// Función para inicializar cálculos automáticos
function inicializarCalculosCorral() {
  const capacidadMaxima = document.getElementById("capacidadMaxima");
  const ocupacionActual = document.getElementById("ocupacionActual");
  const porcentajeOcupacion = document.getElementById("porcentajeOcupacion");

  if (capacidadMaxima && ocupacionActual && porcentajeOcupacion) {
    capacidadMaxima.addEventListener("input", calcularPorcentajeOcupacion);
    ocupacionActual.addEventListener("input", calcularPorcentajeOcupacion);
  }
}

// Función para calcular porcentaje de ocupación
function calcularPorcentajeOcupacion() {
  const capacidadMaxima =
    parseFloat(document.getElementById("capacidadMaxima").value) || 0;
  const ocupacionActual =
    parseFloat(document.getElementById("ocupacionActual").value) || 0;
  const porcentajeOcupacion = document.getElementById("porcentajeOcupacion");

  if (capacidadMaxima > 0) {
    const porcentaje = (ocupacionActual / capacidadMaxima) * 100;
    porcentajeOcupacion.value = porcentaje.toFixed(1);
  } else {
    porcentajeOcupacion.value = "";
  }
}

// Función para guardar los datos del corral
function guardarCorral() {
  // Validar datos antes de guardar
  if (!validarFormularioCorral()) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  // Recopilar datos del formulario
  const datosCorral = {
    codigo: document.getElementById("codigoCorral").value,
    nombre: document.getElementById("nombreCorral").value,
    tipo: document.getElementById("tipoCorral").value,
    ubicacion: document.getElementById("ubicacionCorral").value,
    estado: document.getElementById("estadoCorral").value,
    areaTotal: document.getElementById("areaTotal").value,
    capacidadMaxima: document.getElementById("capacidadMaxima").value,
    // ... recopilar todos los demás campos
  };

  // Aquí iría la lógica para guardar en base de datos
  console.log("Guardando corral:", datosCorral);

  // Cerrar modal después de guardar
  cerrarModal("modalCorrales");

  // Actualizar interfaz si es necesario
  actualizarListaCorrales();
}

// Función para validar el formulario
function validarFormularioCorral() {
  const camposRequeridos = [
    "codigoCorral",
    "nombreCorral",
    "tipoCorral",
    "ubicacionCorral",
    "estadoCorral",
    "areaTotal",
    "capacidadMaxima",
  ];

  for (let campo of camposRequeridos) {
    if (!document.getElementById(campo).value) {
      document.getElementById(campo).focus();
      return false;
    }
  }

  return true;
}

// Función para actualizar la lista de corrales en la interfaz
function actualizarListaCorrales() {
  // Lógica para actualizar la visualización de corrales
  // Esto podría incluir actualizar un contador o una tabla de corrales
  console.log("Actualizando lista de corrales...");
}

// Función para cargar datos existentes al editar
function cargarDatosCorral(corralId) {
  // Aquí iría la lógica para cargar datos existentes de un corral
  // desde la base de datos cuando se esté editando
  console.log("Cargando datos del corral:", corralId);
}

// Función para cargar corrales disponibles
function cargarCorralesDisponibles() {
  // En una implementación real, esto vendría de una base de datos
  const corrales = [
    { codigo: "COR-001", nombre: "Corral Engorde Norte" },
    { codigo: "COR-002", nombre: "Corral Maternidad Sur" },
    { codigo: "COR-003", nombre: "Corral Recría Este" },
    { codigo: "COR-004", nombre: "Corral Aislamiento Oeste" },
  ];

  const selectCorral = document.getElementById("corralAsignado");
  selectCorral.innerHTML = '<option value="">Seleccionar corral</option>';

  corrales.forEach((corral) => {
    const option = document.createElement("option");
    option.value = corral.codigo;
    option.textContent = `${corral.codigo} - ${corral.nombre}`;
    selectCorral.appendChild(option);
  });
}

// Función para inicializar cálculos automáticos
function inicializarCalculosLote() {
  // Calcular mortalidad actual
  const totalAnimales = document.getElementById("totalAnimales");
  const mortalidadActual = document.getElementById("mortalidadActual");

  if (totalAnimales && mortalidadActual) {
    totalAnimales.addEventListener("input", calcularMortalidadActual);
  }

  // Calcular costos totales
  const costosAlimentacion = document.getElementById(
    "costosAlimentacionMensual"
  );
  const costosSanitarios = document.getElementById("costosSanitariosMensual");
  const costosManoObra = document.getElementById("costosManoObraMensual");
  const costosTotales = document.getElementById("costosTotalesMensual");

  if (
    costosAlimentacion &&
    costosSanitarios &&
    costosManoObra &&
    costosTotales
  ) {
    costosAlimentacion.addEventListener("input", calcularCostosTotales);
    costosSanitarios.addEventListener("input", calcularCostosTotales);
    costosManoObra.addEventListener("input", calcularCostosTotales);
  }
}

// Función para calcular mortalidad actual
function calcularMortalidadActual() {
  // En una implementación real, esto calcularía la mortalidad basada en registros
  console.log("Calculando mortalidad actual...");
}

// Función para calcular costos totales
function calcularCostosTotales() {
  const alimentacion =
    parseFloat(document.getElementById("costosAlimentacionMensual").value) || 0;
  const sanitarios =
    parseFloat(document.getElementById("costosSanitariosMensual").value) || 0;
  const manoObra =
    parseFloat(document.getElementById("costosManoObraMensual").value) || 0;
  const costosTotales = document.getElementById("costosTotalesMensual");

  const total = alimentacion + sanitarios + manoObra;
  costosTotales.value = total.toFixed(2);
}

function guardarLote() {
  if (!validarFormularioLote()) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  const datosLote = {
    codigo: document.getElementById("codigoLote").value,
    nombre: document.getElementById("nombreLote").value,
    tipo: document.getElementById("tipoLote").value,
    categoria: document.getElementById("categoriaLote").value,
    fechaCreacion: document.getElementById("fechaCreacionLote").value,
    estado: document.getElementById("estadoLote").value,
    totalAnimales: document.getElementById("totalAnimales").value,
    razaPrincipal: document.getElementById("razaPrincipal").value,
    sistemaAlimentacion: document.getElementById("sistemaAlimentacion").value,
    responsable: document.getElementById("responsableLote").value,
  };

  console.log("Guardando lote:", datosLote);

  cerrarModal("modalLotes");

  // Actualizar interfaz si es necesario
  actualizarListaLotes();
}

// Función para validar el formulario
function validarFormularioLote() {
  const camposRequeridos = [
    "codigoLote",
    "nombreLote",
    "tipoLote",
    "categoriaLote",
    "fechaCreacionLote",
    "estadoLote",
    "totalAnimales",
    "razaPrincipal",
    "sistemaAlimentacion",
    "responsableLote",
  ];

  for (let campo of camposRequeridos) {
    if (!document.getElementById(campo).value) {
      document.getElementById(campo).focus();
      return false;
    }
  }

  return true;
}

// Función para actualizar la lista de lotes en la interfaz
function actualizarListaLotes() {
  // Lógica para actualizar la visualización de lotes
  console.log("Actualizando lista de lotes...");
}

// Función para cargar datos existentes al editar
function cargarDatosLote(loteId) {
  // Aquí iría la lógica para cargar datos existentes de un lote
  console.log("Cargando datos del lote:", loteId);
}

// Función para cargar opciones disponibles
function cargarOpcionesDisponibles() {
  // Cargar lotes disponibles
  cargarLotesDisponibles();
  // Cargar corrales disponibles
  cargarCorralesDisponibles();
}

// Función para cargar lotes disponibles
function cargarLotesDisponibles() {
  // En una implementación real, esto vendría de una base de datos
  const lotes = [
    { codigo: "LOT-001", nombre: "Lote Engorde Norte" },
    { codigo: "LOT-002", nombre: "Lote Vientres Sur" },
    { codigo: "LOT-003", nombre: "Lote Recría Este" },
    { codigo: "LOT-004", nombre: "Lote Terneros Oeste" },
  ];

  const selectLote = document.getElementById("loteAsociado");
  selectLote.innerHTML = '<option value="">Seleccionar lote</option>';

  lotes.forEach((lote) => {
    const option = document.createElement("option");
    option.value = lote.codigo;
    option.textContent = `${lote.codigo} - ${lote.nombre}`;
    selectLote.appendChild(option);
  });
}

// Función para cargar corrales disponibles
function cargarCorralesDisponibles() {
  // En una implementación real, esto vendría de una base de datos
  const corrales = [
    { codigo: "COR-001", nombre: "Corral Engorde Norte" },
    { codigo: "COR-002", nombre: "Corral Maternidad Sur" },
    { codigo: "COR-003", nombre: "Corral Recría Este" },
    { codigo: "COR-004", nombre: "Corral Aislamiento Oeste" },
  ];

  const selectCorral = document.getElementById("corralAsignado");
  selectCorral.innerHTML = '<option value="">Seleccionar corral</option>';

  corrales.forEach((corral) => {
    const option = document.createElement("option");
    option.value = corral.codigo;
    option.textContent = `${corral.codigo} - ${corral.nombre}`;
    selectCorral.appendChild(option);
  });
}

// Función para inicializar funcionalidades del grupo
function inicializarFuncionalidadesGrupo() {
  // Calcular progreso automáticamente
  const fechaInicio = document.getElementById("fechaInicioSeguimientoGrupo");
  const fechaFinalizacion = document.getElementById(
    "fechaEstimadaFinalizacion"
  );
  const progresoActual = document.getElementById("progresoActual");

  if (fechaInicio && fechaFinalizacion && progresoActual) {
    fechaInicio.addEventListener("change", calcularProgreso);
    fechaFinalizacion.addEventListener("change", calcularProgreso);
  }

  // Validar criterios de composición
  const edadMinima = document.getElementById("edadMinima");
  const edadMaxima = document.getElementById("edadMaxima");
  const pesoMinimo = document.getElementById("pesoMinimo");
  const pesoMaximo = document.getElementById("pesoMaximo");

  if (edadMinima && edadMaxima) {
    edadMinima.addEventListener("change", validarRangoEdad);
    edadMaxima.addEventListener("change", validarRangoEdad);
  }

  if (pesoMinimo && pesoMaximo) {
    pesoMinimo.addEventListener("change", validarRangoPeso);
    pesoMaximo.addEventListener("change", validarRangoPeso);
  }
}

// Función para calcular progreso del grupo
function calcularProgreso() {
  const fechaInicio = document.getElementById(
    "fechaInicioSeguimientoGrupo"
  ).value;
  const fechaFinalizacion = document.getElementById(
    "fechaEstimadaFinalizacion"
  ).value;
  const progresoActual = document.getElementById("progresoActual");

  if (fechaInicio && fechaFinalizacion) {
    const inicio = new Date(fechaInicio);
    const finalizacion = new Date(fechaFinalizacion);
    const hoy = new Date();

    if (hoy >= inicio && hoy <= finalizacion) {
      const totalDias = (finalizacion - inicio) / (1000 * 60 * 60 * 24);
      const diasTranscurridos = (hoy - inicio) / (1000 * 60 * 60 * 24);
      const progreso = (diasTranscurridos / totalDias) * 100;
      progresoActual.value = Math.min(100, Math.max(0, progreso.toFixed(1)));
    } else if (hoy > finalizacion) {
      progresoActual.value = 100;
    } else {
      progresoActual.value = 0;
    }
  }
}

// Función para validar rango de edad
function validarRangoEdad() {
  const edadMinima = parseInt(document.getElementById("edadMinima").value) || 0;
  const edadMaxima = parseInt(document.getElementById("edadMaxima").value) || 0;

  if (edadMinima > 0 && edadMaxima > 0 && edadMinima > edadMaxima) {
    alert("La edad mínima no puede ser mayor que la edad máxima");
    document.getElementById("edadMinima").value = "";
    document.getElementById("edadMaxima").value = "";
  }
}

// Función para validar rango de peso
function validarRangoPeso() {
  const pesoMinimo =
    parseFloat(document.getElementById("pesoMinimo").value) || 0;
  const pesoMaximo =
    parseFloat(document.getElementById("pesoMaximo").value) || 0;

  if (pesoMinimo > 0 && pesoMaximo > 0 && pesoMinimo > pesoMaximo) {
    alert("El peso mínimo no puede ser mayor que el peso máximo");
    document.getElementById("pesoMinimo").value = "";
    document.getElementById("pesoMaximo").value = "";
  }
}

// Función para guardar los datos del grupo
function guardarGrupo() {
  // Validar datos antes de guardar
  if (!validarFormularioGrupo()) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  // Recopilar datos del formulario
  const datosGrupo = {
    codigo: document.getElementById("codigoGrupo").value,
    nombre: document.getElementById("nombreGrupo").value,
    tipo: document.getElementById("tipoGrupo").value,
    categoria: document.getElementById("categoriaGrupo").value,
    fechaCreacion: document.getElementById("fechaCreacionGrupo").value,
    estado: document.getElementById("estadoGrupo").value,
    totalAnimales: document.getElementById("totalAnimalesGrupo").value,
    objetivo: document.getElementById("objetivoGrupo").value,
    responsable: document.getElementById("responsableGrupo").value,
    // ... recopilar todos los demás campos
  };

  // Aquí iría la lógica para guardar en base de datos
  console.log("Guardando grupo:", datosGrupo);

  // Cerrar modal después de guardar
  cerrarModal("modalGrupos");

  // Actualizar interfaz si es necesario
  actualizarListaGrupos();
}

// Función para validar el formulario
function validarFormularioGrupo() {
  const camposRequeridos = [
    "codigoGrupo",
    "nombreGrupo",
    "tipoGrupo",
    "categoriaGrupo",
    "fechaCreacionGrupo",
    "estadoGrupo",
    "totalAnimalesGrupo",
    "objetivoGrupo",
    "responsableGrupo",
  ];

  for (let campo of camposRequeridos) {
    if (!document.getElementById(campo).value) {
      document.getElementById(campo).focus();
      return false;
    }
  }

  return true;
}

// Función para actualizar la lista de grupos en la interfaz
function actualizarListaGrupos() {
  // Lógica para actualizar la visualización de grupos
  console.log("Actualizando lista de grupos...");
}

// Función para cargar datos existentes al editar
function cargarDatosGrupo(grupoId) {
  // Aquí iría la lógica para cargar datos existentes de un grupo
  console.log("Cargando datos del grupo:", grupoId);
}

// Función para inicializar funcionalidades del color
function inicializarFuncionalidadesColor() {
  // Preview de color hexadecimal
  const hexInput = document.getElementById("codigoHexadecimal");
  const colorPreview = document.getElementById("colorPreview");

  if (hexInput && colorPreview) {
    hexInput.addEventListener("input", function () {
      actualizarPreviewColor(this.value);
    });

    // Validar formato hexadecimal
    hexInput.addEventListener("blur", function () {
      validarFormatoHexadecimal(this.value);
    });
  }

  // Calcular estadísticas automáticamente
  inicializarCalculosEstadisticos();
}

// Función para actualizar el preview del color
function actualizarPreviewColor(hexColor) {
  const colorPreview = document.getElementById("colorPreview");
  if (colorPreview && /^#[0-9A-F]{6}$/i.test(hexColor)) {
    colorPreview.style.backgroundColor = hexColor;
  } else {
    colorPreview.style.backgroundColor = "#ffffff";
  }
}

// Función para validar formato hexadecimal
function validarFormatoHexadecimal(hexColor) {
  const hexInput = document.getElementById("codigoHexadecimal");
  if (hexColor && !/^#[0-9A-F]{6}$/i.test(hexColor)) {
    alert("Por favor ingrese un código hexadecimal válido (ej: #FF0000)");
    hexInput.value = "";
    hexInput.focus();
    return false;
  }
  return true;
}

// Función para inicializar cálculos estadísticos
function inicializarCalculosEstadisticos() {
  // En una implementación real, estos datos vendrían de la base de datos
  // Por ahora, los inicializamos como campos de solo lectura
  document.getElementById("totalAnimalesColor").value = "0";
  document.getElementById("porcentajePoblacion").value = "0.0";
}

// Función para guardar los datos del color
function guardarColor() {
  // Validar datos antes de guardar
  if (!validarFormularioColor()) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  // Validar código hexadecimal si se proporcionó
  const hexColor = document.getElementById("codigoHexadecimal").value;
  if (hexColor && !validarFormatoHexadecimal(hexColor)) {
    return;
  }

  // Recopilar datos del formulario
  const datosColor = {
    codigo: document.getElementById("codigoColor").value,
    nombre: document.getElementById("nombreColor").value,
    tipo: document.getElementById("tipoColor").value,
    categoria: document.getElementById("categoriaColor").value,
    estado: document.getElementById("estadoColor").value,
    descripcion: document.getElementById("descripcionColor").value,
    codigoHexadecimal: hexColor,
    familia: document.getElementById("familiaColor").value,
    intensidad: document.getElementById("intensidadColor").value,
    patron: document.getElementById("patronColor").value,
    herencia: document.getElementById("herenciaColor").value,
    preferenciaMercado: document.getElementById("preferenciaMercado").value,
    valorComercial: document.getElementById("valorComercial").value,
    // ... recopilar todos los demás campos
  };

  // Aquí iría la lógica para guardar en base de datos
  console.log("Guardando color:", datosColor);

  // Cerrar modal después de guardar
  cerrarModal("modalColores");

  // Actualizar interfaz si es necesario
  actualizarListaColores();
}

// Función para validar el formulario
function validarFormularioColor() {
  const camposRequeridos = [
    "codigoColor",
    "nombreColor",
    "tipoColor",
    "categoriaColor",
    "estadoColor",
    "descripcionColor",
  ];

  for (let campo of camposRequeridos) {
    if (!document.getElementById(campo).value) {
      document.getElementById(campo).focus();
      return false;
    }
  }

  return true;
}

// Función para actualizar la lista de colores en la interfaz
function actualizarListaColores() {
  // Lógica para actualizar la visualización de colores
  console.log("Actualizando lista de colores...");

  // Actualizar contador en la tarjeta de colores
  const dataCard = document.querySelector(
    '.data-card:has(button[onclick*="modalColores"])'
  );
  if (dataCard) {
    const dataValue = dataCard.querySelector(".data-value");
    if (dataValue) {
      const currentCount = parseInt(dataValue.textContent) || 0;
      dataValue.textContent = currentCount + 1;
    }
  }
}

// Función para cargar datos existentes al editar
function cargarDatosColor(colorId) {
  // Aquí iría la lógica para cargar datos existentes de un color
  console.log("Cargando datos del color:", colorId);

  // Simular carga de datos
  const datosEjemplo = {
    codigo: "COL-001",
    nombre: "Rojo Cereza",
    tipo: "basico",
    categoria: "sólido",
    estado: "activo",
    descripcion: "Color rojo intenso similar al de las cerezas maduras",
    codigoHexadecimal: "#DC143C",
    familia: "rojos",
    intensidad: "medio",
    patron: "uniforme",
    herencia: "dominante",
    preferenciaMercado: "alta",
    valorComercial: "premium",
  };

  Object.keys(datosEjemplo).forEach((key) => {
    const element = document.getElementById(key);
    if (element) {
      element.value = datosEjemplo[key];
    }
  });

  actualizarPreviewColor(datosEjemplo.codigoHexadecimal);
}

function generarCodigoColor() {
  const nombre = document.getElementById("nombreColor").value;
  if (nombre) {
    const codigo =
      "COL-" +
      nombre.substring(0, 3).toUpperCase() +
      "-" +
      Math.random().toString(36).substr(2, 3).toUpperCase();
    document.getElementById("codigoColor").value = codigo;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const nombreColorInput = document.getElementById("nombreColor");
  if (nombreColorInput) {
    nombreColorInput.addEventListener("blur", function () {
      if (!document.getElementById("codigoColor").value) {
        generarCodigoColor();
      }
    });
  }
});

// Función para inicializar funcionalidades del cruce
function inicializarFuncionalidadesCruce() {
  // Mostrar/ocultar parentales adicionales según tipo de cruce
  const tipoCruce = document.getElementById("tipoCruce");
  if (tipoCruce) {
    tipoCruce.addEventListener("change", function () {
      manejarParentalesAdicionales(this.value);
    });
  }

  // Validar porcentajes parentales
  const porcentajePadre = document.getElementById("porcentajePadre");
  const porcentajeMadre = document.getElementById("porcentajeMadre");

  if (porcentajePadre && porcentajeMadre) {
    porcentajePadre.addEventListener("change", validarPorcentajesParentales);
    porcentajeMadre.addEventListener("change", validarPorcentajesParentales);
  }

  // Generar código automático
  const nombreCruce = document.getElementById("nombreCruce");
  if (nombreCruce) {
    nombreCruce.addEventListener("blur", function () {
      if (!document.getElementById("codigoCruce").value) {
        generarCodigoCruce();
      }
    });
  }
}

// Función para manejar parentales adicionales
function manejarParentalesAdicionales(tipoCruce) {
  const parentalTercero = document.getElementById("parental-tercero");
  const parentalCuarto = document.getElementById("parental-cuarto");

  if (!parentalTercero || !parentalCuarto) return;

  // Ocultar todos inicialmente
  parentalTercero.style.display = "none";
  parentalCuarto.style.display = "none";

  // Mostrar según tipo de cruce
  switch (tipoCruce) {
    case "tres-vias":
    case "rotacional":
      parentalTercero.style.display = "block";
      break;
    case "cuatro-vias":
    case "composite":
      parentalTercero.style.display = "block";
      parentalCuarto.style.display = "block";
      break;
  }
}

// Función para validar porcentajes parentales
function validarPorcentajesParentales() {
  const porcentajePadre =
    parseFloat(document.getElementById("porcentajePadre").value) || 0;
  const porcentajeMadre =
    parseFloat(document.getElementById("porcentajeMadre").value) || 0;
  const tipoCruce = document.getElementById("tipoCruce").value;

  let total = porcentajePadre + porcentajeMadre;

  // Validar según tipo de cruce
  if (tipoCruce === "simple" && total !== 100) {
    alert("Para cruce simple, la suma de porcentajes debe ser 100%");
    document.getElementById("porcentajePadre").value = "";
    document.getElementById("porcentajeMadre").value = "";
    return false;
  }

  return true;
}

// Función para generar código de cruce automáticamente
function generarCodigoCruce() {
  const nombre = document.getElementById("nombreCruce").value;
  const tipo = document.getElementById("tipoCruce").value;

  if (nombre && tipo) {
    const codigo =
      "CRU-" +
      tipo.substring(0, 3).toUpperCase() +
      "-" +
      nombre.substring(0, 3).toUpperCase() +
      "-" +
      Math.random().toString(36).substr(2, 3).toUpperCase();
    document.getElementById("codigoCruce").value = codigo;
  }
}

// Función para calcular resultados esperados automáticamente
function calcularResultadosEsperados() {
  // En una implementación real, esto calcularía basado en datos genéticos
  // Por ahora es una simulación
  const razaPadre = document.getElementById("razaPadre").value;
  const razaMadre = document.getElementById("razaMadre").value;

  if (razaPadre && razaMadre) {
    // Simular cálculos basados en razas parentales
    const resultados = calcularPorRazas(razaPadre, razaMadre);

    // Actualizar campos con resultados calculados
    Object.keys(resultados).forEach((key) => {
      const element = document.getElementById(key);
      if (element) {
        element.value = resultados[key];
      }
    });
  }
}

// Función de ejemplo para cálculos por razas
function calcularPorRazas(razaPadre, razaMadre) {
  // Tabla de resultados esperados por combinación de razas
  const combinaciones = {
    "angus-brahman": {
      gananciaDiariaEsperada: 1200,
      pesoAdultoEsperado: 650,
      rendimientoCanalEsperado: 58,
      adaptabilidadClimaticaEsperada: "buena",
    },
    "hereford-brahman": {
      gananciaDiariaEsperada: 1150,
      pesoAdultoEsperado: 630,
      rendimientoCanalEsperado: 57,
      adaptabilidadClimaticaEsperada: "buena",
    },
    default: {
      gananciaDiariaEsperada: 1000,
      pesoAdultoEsperado: 600,
      rendimientoCanalEsperado: 55,
      adaptabilidadClimaticaEsperada: "regular",
    },
  };

  const clave = `${razaPadre}-${razaMadre}`.toLowerCase();
  return combinaciones[clave] || combinaciones["default"];
}

// Función para guardar los datos del cruce
function guardarCruce() {
  // Validar datos antes de guardar
  if (!validarFormularioCruce()) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  // Validar porcentajes parentales
  if (!validarPorcentajesParentales()) {
    return;
  }

  // Recopilar datos del formulario
  const datosCruce = {
    codigo: document.getElementById("codigoCruce").value,
    nombre: document.getElementById("nombreCruce").value,
    tipo: document.getElementById("tipoCruce").value,
    categoria: document.getElementById("categoriaCruce").value,
    estado: document.getElementById("estadoCruce").value,
    objetivo: document.getElementById("objetivoPrincipal").value,
    descripcion: document.getElementById("descripcionCruce").value,
    razaPadre: document.getElementById("razaPadre").value,
    porcentajePadre: document.getElementById("porcentajePadre").value,
    razaMadre: document.getElementById("razaMadre").value,
    porcentajeMadre: document.getElementById("porcentajeMadre").value,
    nivelVigorHibrido: document.getElementById("nivelVigorHibrido").value,
    // ... recopilar todos los demás campos
  };

  // Aquí iría la lógica para guardar en base de datos
  console.log("Guardando cruce:", datosCruce);

  // Cerrar modal después de guardar
  cerrarModal("modalCruces");

  // Actualizar interfaz si es necesario
  actualizarListaCruces();
}

// Función para validar el formulario
function validarFormularioCruce() {
  const camposRequeridos = [
    "codigoCruce",
    "nombreCruce",
    "tipoCruce",
    "categoriaCruce",
    "estadoCruce",
    "objetivoPrincipal",
    "descripcionCruce",
    "razaPadre",
    "porcentajePadre",
    "razaMadre",
    "porcentajeMadre",
  ];

  for (let campo of camposRequeridos) {
    if (!document.getElementById(campo).value) {
      document.getElementById(campo).focus();
      return false;
    }
  }

  return true;
}

// Función para actualizar la lista de cruces en la interfaz
function actualizarListaCruces() {
  // Lógica para actualizar la visualización de cruces
  console.log("Actualizando lista de cruces...");

  // Actualizar contador en la tarjeta de cruces
  const dataCard = document.querySelector(
    '.data-card:has(button[onclick*="modalCruces"])'
  );
  if (dataCard) {
    const dataValue = dataCard.querySelector(".data-value");
    if (dataValue) {
      const currentCount = parseInt(dataValue.textContent) || 0;
      dataValue.textContent = currentCount + 1;
    }
  }
}

// Función para cargar datos existentes al editar
function cargarDatosCruce(cruceId) {
  // Aquí iría la lógica para cargar datos existentes de un cruce
  console.log("Cargando datos del cruce:", cruceId);
}

// Asignar evento para calcular resultados al cambiar parentales
document.addEventListener("DOMContentLoaded", function () {
  const razaPadre = document.getElementById("razaPadre");
  const razaMadre = document.getElementById("razaMadre");

  if (razaPadre && razaMadre) {
    razaPadre.addEventListener("change", calcularResultadosEsperados);
    razaMadre.addEventListener("change", calcularResultadosEsperados);
  }
});

// Función para inicializar funcionalidades de la categoría de notas
function inicializarFuncionalidadesCategoriaNota() {
  // Preview de color de categoría
  const colorInput = document.getElementById("colorCategoria");
  const colorPreview = document.getElementById("colorPreviewCategoria");

  if (colorInput && colorPreview) {
    colorInput.addEventListener("input", function () {
      colorPreview.style.backgroundColor = this.value;
    });
  }

  // Generar código automático
  const nombreCategoria = document.getElementById("nombreCategoria");
  if (nombreCategoria) {
    nombreCategoria.addEventListener("blur", function () {
      if (!document.getElementById("codigoCategoria").value) {
        generarCodigoCategoria();
      }
    });
  }

  // Actualizar estadísticas automáticamente
  actualizarEstadisticasCategoria();

  // Configurar fecha de creación por defecto
  const fechaCreacion = document.getElementById("fechaCreacionCategoria");
  if (fechaCreacion && !fechaCreacion.value) {
    fechaCreacion.value = new Date().toISOString().split("T")[0];
  }
}

// Función para generar código de categoría automáticamente
function generarCodigoCategoria() {
  const nombre = document.getElementById("nombreCategoria").value;
  const tipo = document.getElementById("tipoCategoria").value;

  if (nombre && tipo) {
    const codigo =
      "CAT-" +
      tipo.substring(0, 3).toUpperCase() +
      "-" +
      nombre.substring(0, 3).toUpperCase() +
      "-" +
      Math.random().toString(36).substr(2, 3).toUpperCase();
    document.getElementById("codigoCategoria").value = codigo;
  }
}

// Función para actualizar estadísticas de la categoría
function actualizarEstadisticasCategoria() {
  // En una implementación real, estos datos vendrían de la base de datos
  // Por ahora, simulamos algunos datos
  const totalNotas = Math.floor(Math.random() * 1000);
  const promedioMensual = (totalNotas / 12).toFixed(1);

  document.getElementById("totalNotasCategoria").value = totalNotas;
  document.getElementById("promedioNotasMes").value = promedioMensual;

  // Simular fecha de última nota (hace 2 días)
  const fechaUltimaNota = new Date();
  fechaUltimaNota.setDate(fechaUltimaNota.getDate() - 2);
  document.getElementById("fechaUltimaNota").value = fechaUltimaNota
    .toISOString()
    .split("T")[0];

  // Usuario aleatorio
  const usuarios = ["admin", "veterinario", "capataz", "supervisor"];
  document.getElementById("usuarioUltimaNota").value =
    usuarios[Math.floor(Math.random() * usuarios.length)];
}

// Función para validar configuraciones de la categoría
function validarConfiguracionCategoria() {
  const formatoNota = document.getElementById("formatoNota").value;
  const longitudMaxima = parseInt(
    document.getElementById("longitudMaxima").value
  );

  if (formatoNota === "numerico" && longitudMaxima > 50) {
    alert(
      "Para formato numérico, se recomienda una longitud máxima menor a 50 caracteres"
    );
    return false;
  }

  if (formatoNota === "si-no" && longitudMaxima !== 1) {
    alert("Para formato Sí/No, la longitud máxima debe ser 1 caracter");
    document.getElementById("longitudMaxima").value = 1;
    return false;
  }

  return true;
}

// Función para guardar los datos de la categoría de notas
function guardarCategoriaNota() {
  // Validar datos antes de guardar
  if (!validarFormularioCategoriaNota()) {
    alert("Por favor, complete todos los campos obligatorios.");
    return;
  }

  // Validar configuración
  if (!validarConfiguracionCategoria()) {
    return;
  }

  // Recopilar datos del formulario
  const datosCategoria = {
    codigo: document.getElementById("codigoCategoria").value,
    nombre: document.getElementById("nombreCategoria").value,
    tipo: document.getElementById("tipoCategoria").value,
    nivel: document.getElementById("nivelCategoria").value,
    estado: document.getElementById("estadoCategoria").value,
    descripcion: document.getElementById("descripcionCategoria").value,
    color: document.getElementById("colorCategoria").value,
    icono: document.getElementById("iconoCategoria").value,
    formato: document.getElementById("formatoNota").value,
    longitudMaxima: document.getElementById("longitudMaxima").value,
    prioridad: document.getElementById("prioridadCategoria").value,
    // ... recopilar todos los demás campos
  };

  // Aquí iría la lógica para guardar en base de datos
  console.log("Guardando categoría de notas:", datosCategoria);

  // Cerrar modal después de guardar
  cerrarModal("modalCategoriasNotas");

  // Actualizar interfaz si es necesario
  actualizarListaCategoriasNotas();
}

// Función para validar el formulario
function validarFormularioCategoriaNota() {
  const camposRequeridos = [
    "codigoCategoria",
    "nombreCategoria",
    "tipoCategoria",
    "nivelCategoria",
    "estadoCategoria",
    "descripcionCategoria",
  ];

  for (let campo of camposRequeridos) {
    if (!document.getElementById(campo).value) {
      document.getElementById(campo).focus();
      return false;
    }
  }

  return true;
}

// Función para actualizar la lista de categorías en la interfaz
function actualizarListaCategoriasNotas() {
  // Lógica para actualizar la visualización de categorías
  console.log("Actualizando lista de categorías de notas...");

  // Actualizar contador en la tarjeta de categorías
  const dataCard = document.querySelector(
    '.data-card:has(button[onclick*="modalCategoriasNotas"])'
  );
  if (dataCard) {
    const dataValue = dataCard.querySelector(".data-value");
    if (dataValue) {
      const currentCount = parseInt(dataValue.textContent) || 0;
      dataValue.textContent = currentCount + 1;
    }
  }
}

// Función para cargar datos existentes al editar
function cargarDatosCategoriaNota(categoriaId) {
  // Aquí iría la lógica para cargar datos existentes de una categoría
  console.log("Cargando datos de la categoría:", categoriaId);

  // Simular carga de datos
  const datosEjemplo = {
    codigoCategoria: "CAT-SAN-ANI-001",
    nombreCategoria: "Tratamientos Veterinarios",
    tipoCategoria: "sanidad",
    nivelCategoria: "animal",
    estadoCategoria: "activa",
    descripcionCategoria:
      "Registro de tratamientos médicos aplicados a los animales",
    colorCategoria: "#e74c3c",
    iconoCategoria: "fas fa-syringe",
    formatoNota: "texto",
    longitudMaxima: 1000,
    prioridadCategoria: "alta",
  };

  // Llenar formulario con datos de ejemplo
  Object.keys(datosEjemplo).forEach((key) => {
    const element = document.getElementById(key);
    if (element) {
      element.value = datosEjemplo[key];
    }
  });

  // Actualizar preview del color
  const colorPreview = document.getElementById("colorPreviewCategoria");
  if (colorPreview) {
    colorPreview.style.backgroundColor = datosEjemplo.colorCategoria;
  }
}

// Función para mostrar/ocultar campos según el tipo de categoría
function configurarCamposPorTipo() {
  const tipoCategoria = document.getElementById("tipoCategoria").value;
  const nivelCategoria = document.getElementById("nivelCategoria").value;

  // Ejemplo: Si es categoría de sanidad a nivel animal, mostrar campos específicos
  if (tipoCategoria === "sanidad" && nivelCategoria === "animal") {
    // Habilitar campos específicos de sanidad animal
  }
}

// Asignar eventos para configuración dinámica
document.addEventListener("DOMContentLoaded", function () {
  const tipoCategoria = document.getElementById("tipoCategoria");
  const nivelCategoria = document.getElementById("nivelCategoria");

  if (tipoCategoria && nivelCategoria) {
    tipoCategoria.addEventListener("change", configurarCamposPorTipo);
    nivelCategoria.addEventListener("change", configurarCamposPorTipo);
  }
});

// Funciones específicas para palpitaciones
function manejarFotosPalpitacion(event) {
  const files = event.target.files;
  const previewContainer = document.getElementById(
    "fotosPreviewContainerPalpitacion"
  );

  if (files.length > 0) {
    previewContainer.innerHTML = "";

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      const reader = new FileReader();

      reader.onload = function (e) {
        const fotoItem = document.createElement("div");
        fotoItem.className = "foto-preview-item";
        fotoItem.innerHTML = `
                    <button type="button" class="eliminar-foto" onclick="eliminarFotoPalpitacion(this)">
                        <i class="fas fa-times"></i>
                    </button>
                    <img src="${e.target.result}" alt="Foto palpitación ${
          i + 1
        }">
                    <div class="foto-descripcion">
                        <textarea placeholder="Descripción de esta imagen de palpitación..."></textarea>
                    </div>
                `;
        previewContainer.appendChild(fotoItem);
      };

      reader.readAsDataURL(file);
    }
  }
}

function eliminarFotoPalpitacion(element) {
  const fotoItem = element.closest(".foto-preview-item");
  fotoItem.remove();

  const previewContainer = document.getElementById(
    "fotosPreviewContainerPalpitacion"
  );
  if (previewContainer.children.length === 0) {
    previewContainer.innerHTML = `
            <div class="no-fotos-message">
                <i class="fas fa-images" style="font-size: 36px; color: #bdc3c7; margin-bottom: 10px;"></i>
                <p>No hay fotos subidas aún</p>
            </div>
        `;
  }
}

function actualizarListaArchivosPalpitacion() {
  const listaArchivos = document.getElementById("listaArchivosPalpitacion");
  const archivos = [
    document.getElementById("imagenesUltrasonido"),
    document.getElementById("informePalpitacion"),
    document.getElementById("resultadosLaboratorio"),
    document.getElementById("facturaVeterinario"),
    document.getElementById("otrosDocumentosPalpitacion"),
  ];

  let archivosSubidos = [];

  archivos.forEach((input) => {
    if (input.files.length > 0) {
      for (let file of input.files) {
        archivosSubidos.push({
          nombre: file.name,
          tipo: file.type,
          tamaño: file.size,
          categoria: obtenerCategoriaArchivo(input.id),
        });
      }
    }
  });

  if (archivosSubidos.length > 0) {
    listaArchivos.innerHTML = "";
    archivosSubidos.forEach((archivo, index) => {
      const archivoItem = document.createElement("div");
      archivoItem.className = "archivo-item";
      archivoItem.innerHTML = `
                <div class="archivo-info">
                    <i class="fas fa-file ${obtenerClaseIcono(
                      archivo.tipo
                    )} archivo-icono"></i>
                    <div>
                        <div style="font-weight: bold;">${archivo.nombre}</div>
                        <div style="font-size: 12px; color: #7f8c8d;">
                            ${archivo.categoria} • ${formatearTamaño(
        archivo.tamaño
      )}
                        </div>
                    </div>
                </div>
                <div class="archivo-acciones">
                    <button type="button" class="btn-ver-archivo" onclick="verArchivoPalpitacion(${index})" title="Vista previa">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn-eliminar-archivo" onclick="eliminarArchivoPalpitacion(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
      listaArchivos.appendChild(archivoItem);
    });
  } else {
    listaArchivos.innerHTML = `
            <div class="archivo-vacio">
                <i class="fas fa-folder-open" style="font-size: 24px; color: #bdc3c7; margin-bottom: 5px;"></i>
                <p>No hay archivos subidos</p>
            </div>
        `;
  }
}

function obtenerCategoriaArchivo(idInput) {
  const categorias = {
    imagenesUltrasonido: "Ultrasonido",
    informePalpitacion: "Informe",
    resultadosLaboratorio: "Laboratorio",
    facturaVeterinario: "Factura",
    otrosDocumentosPalpitacion: "Otros",
  };
  return categorias[idInput] || "Documento";
}

function verArchivoPalpitacion(index) {
  // Aquí iría la lógica para previsualizar el archivo
  alert("Funcionalidad de vista previa en desarrollo");
}

function eliminarArchivoPalpitacion(index) {
  // Aquí iría la lógica para eliminar el archivo específico
  if (confirm("¿Está seguro de que desea eliminar este archivo?")) {
    alert("Archivo eliminado (funcionalidad en desarrollo)");
    actualizarListaArchivosPalpitacion();
  }
}

function guardarPalpitacionCompleta() {
  if (validarPalpitacionCompleta()) {
    guardarPalpitacion();
  }
}

function validarPalpitacionCompleta() {
  // Validar que al menos haya evidencia fotográfica si el diagnóstico es positivo
  const resultado = document.getElementById("resultadoPalpitacion").value;
  const fotosSubidas = document.getElementById(
    "fotosPreviewContainerPalpitacion"
  ).children.length;

  if (resultado === "preñada" && fotosSubidas === 0) {
    if (
      !confirm(
        "El diagnóstico es positivo pero no se han subido imágenes de evidencia. ¿Desea continuar?"
      )
    ) {
      return false;
    }
  }

  return true;
}

// Inicializar eventos específicos para palpitaciones
document.addEventListener("DOMContentLoaded", function () {
  // Evento para el área de carga de fotos de palpitación
  const fotoUploadArea = document.getElementById("fotoUploadAreaPalpitacion");
  const fotosInput = document.getElementById("fotosPalpitacion");

  if (fotoUploadArea && fotosInput) {
    fotoUploadArea.addEventListener("click", () => fotosInput.click());
    fotoUploadArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      fotoUploadArea.style.backgroundColor = "#e3f2fd";
    });
    fotoUploadArea.addEventListener("dragleave", () => {
      fotoUploadArea.style.backgroundColor = "#f8f9fa";
    });
    fotoUploadArea.addEventListener("drop", (e) => {
      e.preventDefault();
      fotoUploadArea.style.backgroundColor = "#f8f9fa";
      fotosInput.files = e.dataTransfer.files;
      manejarFotosPalpitacion({ target: fotosInput });
    });
  }

  // Eventos para actualizar lista de archivos de palpitación
  const inputsArchivosPalpitacion = [
    "imagenesUltrasonido",
    "informePalpitacion",
    "resultadosLaboratorio",
    "facturaVeterinario",
    "otrosDocumentosPalpitacion",
  ];

  inputsArchivosPalpitacion.forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("change", actualizarListaArchivosPalpitacion);
    }
  });

  // Auto-completar fecha de documentación
  const fechaDocInput = document.getElementById(
    "fechaDocumentacionPalpitacion"
  );
  if (fechaDocInput) {
    fechaDocInput.value = new Date().toISOString().split("T")[0];
  }

  // Auto-completar responsable si hay veterinario registrado
  const veterinarioInput = document.getElementById("veterinarioPalpitacion");
  const responsableDocInput = document.getElementById(
    "responsableDocumentacionPalpitacion"
  );

  if (veterinarioInput && responsableDocInput) {
    veterinarioInput.addEventListener("change", function () {
      if (this.value && !responsableDocInput.value) {
        responsableDocInput.value = this.value;
      }
    });
  }
});



// Actualizar vista previa del color
document.getElementById('color-codigo').addEventListener('input', function() {
    document.getElementById('color-preview').style.backgroundColor = this.value;
});

// Limpiar formulario
document.getElementById('limpiar-color').addEventListener('click', function() {
    document.getElementById('form-color').reset();
    document.getElementById('color-preview').style.backgroundColor = '#000000';
});

// Manejar envío del formulario
document.getElementById('form-color').addEventListener('submit', function(e) {
    e.preventDefault();
    // Aquí iría la lógica para guardar el color
    alert('Color guardado correctamente');
});

// Manejar clics en botones de editar y eliminar
document.addEventListener('click', function(e) {
    if (e.target.closest('.editar-color')) {
        // Lógica para editar color
        alert('Editar color');
    }
    
    if (e.target.closest('.eliminar-color')) {
        // Lógica para eliminar color
        if (confirm('¿Está seguro de que desea eliminar este color?')) {
            alert('Color eliminado');
        }
    }
});



// Variables globales
let registroAEliminar = null;
let tablaAEliminar = null;

// Función para cargar datos en las tablas
function cargarDatosTablas() {
    // Simulación de datos - en producción, estos vendrían de una API
    const datosVientres = [
        { id: 1, codigo: "V001", nombre: "Bella", raza: "Brahman", edad: 36, peso: 450, estado: "Preñada" },
        { id: 2, codigo: "V002", nombre: "Luna", raza: "Angus", edad: 28, peso: 420, estado: "Servida" },
        { id: 3, codigo: "V003", nombre: "Estrella", raza: "Holstein", edad: 42, peso: 480, estado: "Lactancia" }
    ];
    
    const datosCria = [
        { id: 1, codigo: "C001", nombre: "Peque", categoria: "Becerro", raza: "Brahman", peso: 120, estado: "Saludable" },
        { id: 2, codigo: "C002", nombre: "Ternerito", categoria: "Becerro", raza: "Angus", peso: 110, estado: "Saludable" }
    ];
    
    // Cargar datos en las tablas
    cargarTabla("tablaVientresBody", datosVientres, "vientres");
    cargarTabla("tablaCriaBody", datosCria, "cria");
    // Agregar más llamadas para las otras tablas...
}

// Función para cargar una tabla específica
function cargarTabla(idTabla, datos, tipo) {
    const tbody = document.getElementById(idTabla);
    tbody.innerHTML = "";
    
    datos.forEach(item => {
        const fila = document.createElement("tr");
        
        // Crear celdas según el tipo de tabla
        if (tipo === "vientres") {
            fila.innerHTML = `
                <td>${item.codigo}</td>
                <td>${item.nombre}</td>
                <td>${item.raza}</td>
                <td>${item.edad}</td>
                <td>${item.peso}</td>
                <td>${item.estado}</td>
                <td class="acciones">
                    <button class="btn-editar" onclick="editarRegistro(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="confirmarEliminar(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
        } else if (tipo === "cria") {
            fila.innerHTML = `
                <td>${item.codigo}</td>
                <td>${item.nombre}</td>
                <td>${item.categoria}</td>
                <td>${item.raza}</td>
                <td>${item.peso}</td>
                <td>${item.estado}</td>
                <td class="acciones">
                    <button class="btn-editar" onclick="editarRegistro(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="confirmarEliminar(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
        }
        // Agregar más condiciones para otros tipos de tablas...
        
        tbody.appendChild(fila);
    });
}

// Función para editar un registro
function editarRegistro(id, tipo) {
    // Aquí deberías cargar los datos del registro en el modal correspondiente
    // Por simplicidad, solo abrimos el modal
    switch(tipo) {
        case 'vientres':
            abrirModal('modalVientres');
            break;
        case 'cria':
            abrirModal('modalCria');
            break;
        case 'eliminados':
            abrirModal('modalEliminados');
            break;
        case 'toros':
            abrirModal('modalToros');
            break;
        case 'inseminadores':
            abrirModal('modalInseminadores');
            break;
        case 'indices':
            abrirModal('modalIndices');
            break;
    }
    
    // En una implementación real, aquí cargarías los datos del registro en el formulario
    console.log(`Editando registro ${id} de tipo ${tipo}`);
}

// Función para confirmar eliminación
function confirmarEliminar(id, tipo) {
    registroAEliminar = id;
    tablaAEliminar = tipo;
    abrirModal('modalConfirmacion');
}

// Función para eliminar un registro
function eliminarRegistro() {
    if (registroAEliminar && tablaAEliminar) {
        // En una implementación real, aquí harías una llamada a la API para eliminar el registro
        console.log(`Eliminando registro ${registroAEliminar} de tipo ${tablaAEliminar}`);
        
        // Simulación de eliminación - recargar la tabla
        cargarDatosTablas();
        
        // Cerrar el modal de confirmación
        cerrarModal('modalConfirmacion');
        
        // Mostrar mensaje de éxito
        alert("Registro eliminado correctamente");
        
        // Resetear variables
        registroAEliminar = null;
        tablaAEliminar = null;
    }
}

// Configurar el botón de confirmación de eliminación
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnConfirmarEliminar').addEventListener('click', eliminarRegistro);
    
    // Cargar datos iniciales
    cargarDatosTablas();
});

// Función para exportar datos a PDF
function exportarDatosPDF() {
    // Aquí implementarías la lógica para exportar a PDF
    alert("Funcionalidad de exportación a PDF en desarrollo");
}




// Variables globales para eventos
let registroEventoAEliminar = null;
let tablaEventoAEliminar = null;

// Función para cargar datos en las tablas de eventos
function cargarDatosTablasEventos() {
    // Simulación de datos - en producción, estos vendrían de una API
    const datosReproductivos = [
        { 
            id: 1, 
            codigoVientre: "V001", 
            tipoServicio: "Inseminación Artificial", 
            fecha: "2024-01-15", 
            toroSemen: "Toro-BRA-001", 
            resultado: "Preñada", 
            responsable: "Dr. Pérez" 
        },
        { 
            id: 2, 
            codigoVientre: "V002", 
            tipoServicio: "Monta Natural", 
            fecha: "2024-01-20", 
            toroSemen: "Toro-ANG-005", 
            resultado: "Pendiente", 
            responsable: "Vaquería" 
        }
    ];
    
    const datosTratamientos = [
        { 
            id: 1, 
            codigoAnimal: "V001", 
            tipoTratamiento: "Antibiótico", 
            fecha: "2024-01-10", 
            diagnostico: "Mastitis", 
            medicamento: "Penicilina", 
            estado: "Completado" 
        },
        { 
            id: 2, 
            codigoAnimal: "C001", 
            tipoTratamiento: "Desparasitación", 
            fecha: "2024-01-18", 
            diagnostico: "Parásitos internos", 
            medicamento: "Ivermectina", 
            estado: "En curso" 
        }
    ];
    
    const datosProduccionLactea = [
        { 
            id: 1, 
            codigoVaca: "V001", 
            fecha: "2024-01-20", 
            produccion: 22.5, 
            grasa: 3.8, 
            proteina: 3.2, 
            turno: "Mañana" 
        },
        { 
            id: 2, 
            codigoVaca: "V003", 
            fecha: "2024-01-20", 
            produccion: 18.2, 
            grasa: 4.1, 
            proteina: 3.4, 
            turno: "Tarde" 
        }
    ];
    
    const datosPesoCorporal = [
        { 
            id: 1, 
            codigoAnimal: "V001", 
            fecha: "2024-01-15", 
            peso: 450, 
            condicionCorporal: "3 - Ideal", 
            variacion: 5.2, 
            responsable: "Juan López" 
        },
        { 
            id: 2, 
            codigoAnimal: "C001", 
            fecha: "2024-01-18", 
            peso: 125, 
            condicionCorporal: "2 - Flaco", 
            variacion: 3.8, 
            responsable: "María García" 
        }
    ];
    
    const datosNotas = [
        { 
            id: 1, 
            titulo: "Comportamiento anormal V001", 
            categoria: "Comportamiento", 
            fecha: "2024-01-19", 
            prioridad: "Alta", 
            estado: "Pendiente", 
            autor: "Dr. Pérez" 
        },
        { 
            id: 2, 
            titulo: "Revisión instalaciones corral 3", 
            categoria: "Instalaciones", 
            fecha: "2024-01-22", 
            prioridad: "Media", 
            estado: "Completada", 
            autor: "Ing. Rodríguez" 
        }
    ];
    
    // Cargar datos en las tablas
    cargarTablaEventos("tablaReproductivosBody", datosReproductivos, "reproductivos");
    cargarTablaEventos("tablaTratamientosBody", datosTratamientos, "tratamientos");
    cargarTablaEventos("tablaProduccionLacteaBody", datosProduccionLactea, "produccion_lactea");
    cargarTablaEventos("tablaPesoCorporalBody", datosPesoCorporal, "peso_corporal");
    cargarTablaEventos("tablaNotasBody", datosNotas, "notas");
}

// Función para cargar una tabla específica de eventos
function cargarTablaEventos(idTabla, datos, tipo) {
    const tbody = document.getElementById(idTabla);
    tbody.innerHTML = "";
    
    datos.forEach(item => {
        const fila = document.createElement("tr");
        
        // Crear celdas según el tipo de tabla
        if (tipo === "reproductivos") {
            const badgeClass = getBadgeClass(item.resultado);
            fila.innerHTML = `
                <td>${item.codigoVientre}</td>
                <td>${item.tipoServicio}</td>
                <td>${item.fecha}</td>
                <td>${item.toroSemen}</td>
                <td><span class="badge ${badgeClass}">${item.resultado}</span></td>
                <td>${item.responsable}</td>
                <td class="acciones">
                    <button class="btn-editar" onclick="editarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="confirmarEliminarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
        } else if (tipo === "tratamientos") {
            const badgeClass = getBadgeClass(item.estado);
            fila.innerHTML = `
                <td>${item.codigoAnimal}</td>
                <td>${item.tipoTratamiento}</td>
                <td>${item.fecha}</td>
                <td>${item.diagnostico}</td>
                <td>${item.medicamento}</td>
                <td><span class="badge ${badgeClass}">${item.estado}</span></td>
                <td class="acciones">
                    <button class="btn-editar" onclick="editarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="confirmarEliminarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
        } else if (tipo === "produccion_lactea") {
            fila.innerHTML = `
                <td>${item.codigoVaca}</td>
                <td>${item.fecha}</td>
                <td>${item.produccion} L</td>
                <td>${item.grasa}%</td>
                <td>${item.proteina}%</td>
                <td>${item.turno}</td>
                <td class="acciones">
                    <button class="btn-editar" onclick="editarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="confirmarEliminarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
        } else if (tipo === "peso_corporal") {
            fila.innerHTML = `
                <td>${item.codigoAnimal}</td>
                <td>${item.fecha}</td>
                <td>${item.peso} kg</td>
                <td>${item.condicionCorporal}</td>
                <td>${item.variacion > 0 ? '+' : ''}${item.variacion} kg</td>
                <td>${item.responsable}</td>
                <td class="acciones">
                    <button class="btn-editar" onclick="editarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="confirmarEliminarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
        } else if (tipo === "notas") {
            const badgeClass = getBadgeClassPrioridad(item.prioridad);
            const estadoClass = getBadgeClass(item.estado);
            fila.innerHTML = `
                <td>${item.titulo}</td>
                <td>${item.categoria}</td>
                <td>${item.fecha}</td>
                <td><span class="badge ${badgeClass}">${item.prioridad}</span></td>
                <td><span class="badge ${estadoClass}">${item.estado}</span></td>
                <td>${item.autor}</td>
                <td class="acciones">
                    <button class="btn-editar" onclick="editarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="confirmarEliminarEvento(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            `;
        }
        
        tbody.appendChild(fila);
    });
}

// Función para determinar la clase del badge según el estado
function getBadgeClass(estado) {
    switch(estado.toLowerCase()) {
        case 'completado':
        case 'completada':
        case 'preñada':
        case 'éxito':
            return 'badge-success';
        case 'en curso':
        case 'pendiente':
        case 'en proceso':
            return 'badge-warning';
        case 'cancelado':
        case 'fallido':
        case 'urgente':
            return 'badge-danger';
        default:
            return 'badge-info';
    }
}

// Función para determinar la clase del badge según la prioridad
function getBadgeClassPrioridad(prioridad) {
    switch(prioridad.toLowerCase()) {
        case 'alta':
        case 'urgente':
            return 'badge-danger';
        case 'media':
            return 'badge-warning';
        case 'baja':
            return 'badge-info';
        default:
            return 'badge-info';
    }
}

// Función para editar un evento
function editarEvento(id, tipo) {
    // Aquí deberías cargar los datos del evento en el modal correspondiente
    switch(tipo) {
        case 'reproductivos':
            abrirModal('modalServicios');
            break;
        case 'tratamientos':
            abrirModal('modalPalpitaciones');
            break;
        case 'produccion_lactea':
            abrirModal('modalProduccionLactea');
            break;
        case 'peso_corporal':
            abrirModal('modalPesoCorporal');
            break;
        case 'notas':
            abrirModal('modalNotas');
            break;
    }
    
    // En una implementación real, aquí cargarías los datos del evento en el formulario
    console.log(`Editando evento ${id} de tipo ${tipo}`);
    
    // Simulación de carga de datos en el formulario
    // cargarDatosEnFormularioEvento(id, tipo);
}

// Función para confirmar eliminación de evento
function confirmarEliminarEvento(id, tipo) {
    registroEventoAEliminar = id;
    tablaEventoAEliminar = tipo;
    abrirModal('modalConfirmacion');
}

// Función para eliminar un evento
function eliminarEvento() {
    if (registroEventoAEliminar && tablaEventoAEliminar) {
        // En una implementación real, aquí harías una llamada a la API para eliminar el evento
        console.log(`Eliminando evento ${registroEventoAEliminar} de tipo ${tablaEventoAEliminar}`);
        
        // Simulación de eliminación - recargar la tabla
        cargarDatosTablasEventos();
        
        // Cerrar el modal de confirmación
        cerrarModal('modalConfirmacion');
        
        // Mostrar mensaje de éxito
        mostrarMensaje("Evento eliminado correctamente", "success");
        
        // Resetear variables
        registroEventoAEliminar = null;
        tablaEventoAEliminar = null;
    }
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo) {
    // Crear elemento de mensaje
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `mensaje-alerta mensaje-${tipo}`;
    mensajeDiv.innerHTML = `
        <span>${mensaje}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    
    // Estilos para el mensaje
    mensajeDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    if (tipo === 'success') {
        mensajeDiv.style.backgroundColor = '#28a745';
    } else if (tipo === 'error') {
        mensajeDiv.style.backgroundColor = '#dc3545';
    } else {
        mensajeDiv.style.backgroundColor = '#17a2b8';
    }
    
    document.body.appendChild(mensajeDiv);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (mensajeDiv.parentElement) {
            mensajeDiv.remove();
        }
    }, 5000);
}

// Configurar el botón de confirmación de eliminación para eventos
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el botón de eliminación para eventos
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', function() {
            // Determinar si es un evento o un dato regular
            if (registroEventoAEliminar !== null) {
                eliminarEvento();
            } else {
                eliminarRegistro(); // Función de la sección de datos
            }
        });
    }
    
    // Cargar datos iniciales de eventos
    cargarDatosTablasEventos();
});

// Función para guardar eventos (ejemplo para producción láctea)
function guardarProduccionLactea() {
    // Aquí iría la lógica para guardar en la base de datos
    // Por ahora, solo recargamos los datos
    cargarDatosTablasEventos();
    cerrarModal('modalProduccionLactea');
    mostrarMensaje("Producción láctea guardada correctamente", "success");
}

// Funciones similares para los otros tipos de eventos...
function guardarServicioCompleto() {
    cargarDatosTablasEventos();
    cerrarModal('modalServicios');
    mostrarMensaje("Servicio reproductivo guardado correctamente", "success");
}

function guardarTratamiento() {
    cargarDatosTablasEventos();
    cerrarModal('modalPalpitaciones');
    mostrarMensaje("Tratamiento guardado correctamente", "success");
}

function guardarPesoCorporal() {
    cargarDatosTablasEventos();
    cerrarModal('modalPesoCorporal');
    mostrarMensaje("Peso corporal guardado correctamente", "success");
}

function guardarNota() {
    cargarDatosTablasEventos();
    cerrarModal('modalNotas');
    mostrarMensaje("Nota guardada correctamente", "success");
}


// Variables globales para configuración
let registroConfigAEliminar = null;
let tablaConfigAEliminar = null;

// Función para cargar datos en las tablas de configuración
function cargarDatosTablasConfiguracion() {
    // Simulación de datos - en producción, estos vendrían de una API
    const datosRazas = [
        { 
            id: 1, 
            codigo: "R-BRA", 
            nombre: "Brahman", 
            tipo: "Carne", 
            origen: "Indico (Cebú)", 
            proposito: "Doble propósito", 
            estado: "Activo" 
        },
        { 
            id: 2, 
            codigo: "R-ANG", 
            nombre: "Angus", 
            tipo: "Carne", 
            origen: "Europeo", 
            proposito: "Producción de carne", 
            estado: "Activo" 
        },
        { 
            id: 3, 
            codigo: "R-HOL", 
            nombre: "Holstein", 
            tipo: "Leche", 
            origen: "Europeo", 
            proposito: "Producción de leche", 
            estado: "Activo" 
        }
    ];
    
    const datosCorrales = [
        { 
            id: 1, 
            codigo: "COR-001", 
            nombre: "Corral Engorde Norte", 
            tipo: "Engorde", 
            ubicacion: "Zona Norte", 
            capacidad: 50, 
            estado: "Activo" 
        },
        { 
            id: 2, 
            codigo: "COR-002", 
            nombre: "Maternidad Central", 
            tipo: "Maternidad", 
            ubicacion: "Zona Centro", 
            capacidad: 25, 
            estado: "Activo" 
        },
        { 
            id: 3, 
            codigo: "COR-003", 
            nombre: "Aislamiento Este", 
            tipo: "Aislamiento", 
            ubicacion: "Zona Este", 
            capacidad: 10, 
            estado: "En mantenimiento" 
        }
    ];
    
    const datosGrupos = [
        { 
            id: 1, 
            codigo: "GRP-001", 
            nombre: "Vacas Lactancia", 
            tipo: "Producción", 
            descripcion: "Vacas en período de lactancia", 
            animales: 24, 
            estado: "Activo" 
        },
        { 
            id: 2, 
            codigo: "GRP-002", 
            nombre: "Novillas Servicio", 
            tipo: "Reproducción", 
            descripcion: "Novillas listas para servicio", 
            animales: 15, 
            estado: "Activo" 
        },
        { 
            id: 3, 
            codigo: "GRP-003", 
            nombre: "Terneros Destete", 
            tipo: "Cría", 
            descripcion: "Terneros recién destetados", 
            animales: 18, 
            estado: "Activo" 
        }
    ];
    
    const datosCruces = [
        { 
            id: 1, 
            codigo: "CRU-001", 
            razaPadre: "Brahman", 
            razaMadre: "Angus", 
            proporcion: "50% Brahman - 50% Angus", 
            proposito: "Adaptabilidad y calidad de carne", 
            estado: "Activo" 
        },
        { 
            id: 2, 
            codigo: "CRU-002", 
            razaPadre: "Holstein", 
            razaMadre: "Jersey", 
            proporcion: "75% Holstein - 25% Jersey", 
            proposito: "Mejorar sólidos en leche", 
            estado: "En evaluación" 
        },
        { 
            id: 3, 
            codigo: "CRU-003", 
            razaPadre: "Brahman", 
            razaMadre: "Hereford", 
            proporcion: "25% Brahman - 75% Hereford", 
            proposito: "Braford comercial", 
            estado: "Activo" 
        }
    ];
    
    const datosDiagnosticos = [
        { 
            id: 1, 
            codigo: "DIA-001", 
            nombre: "Mastitis Clínica", 
            categoria: "Ubre", 
            sintomas: "Inflamación, dolor, leche anormal", 
            gravedad: "Alta", 
            estado: "Activo" 
        },
        { 
            id: 2, 
            codigo: "DIA-002", 
            nombre: "Queratoconjuntivitis", 
            categoria: "Oftalmológica", 
            sintomas: "Lagrimeo, opacidad corneal, fotofobia", 
            gravedad: "Media", 
            estado: "Activo" 
        },
        { 
            id: 3, 
            codigo: "DIA-003", 
            nombre: "Parásitos Gastrointestinales", 
            categoria: "Parasitología", 
            sintomas: "Diarrea, pérdida de peso, anemia", 
            gravedad: "Media", 
            estado: "Activo" 
        }
    ];
    
    // Cargar datos en las tablas
    cargarTablaConfiguracion("tablaRazasBody", datosRazas, "razas");
    cargarTablaConfiguracion("tablaCorralesBody", datosCorrales, "corrales");
    cargarTablaConfiguracion("tablaGruposBody", datosGrupos, "grupos");
    cargarTablaConfiguracion("tablaCrucesBody", datosCruces, "cruces");
    cargarTablaConfiguracion("tablaDiagnosticosBody", datosDiagnosticos, "diagnosticos");
}

// Función para cargar una tabla específica de configuración
function cargarTablaConfiguracion(idTabla, datos, tipo) {
    const tbody = document.getElementById(idTabla);
    tbody.innerHTML = "";
    
    if (datos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="tabla-vacia">
                    <i class="fas fa-inbox"></i>
                    <p>No hay registros disponibles</p>
                    <small>Haz clic en "Agregar" para crear el primer registro</small>
                </td>
            </tr>
        `;
        return;
    }
    
    datos.forEach(item => {
        const fila = document.createElement("tr");
        fila.className = "tabla-configuracion";
        
        // Crear celdas según el tipo de tabla
        if (tipo === "razas") {
            const badgeClass = item.estado === "Activo" ? "badge-activo" : "badge-inactivo";
            fila.innerHTML = `
                <td><strong>${item.codigo}</strong></td>
                <td>${item.nombre}</td>
                <td><span class="badge badge-config">${item.tipo}</span></td>
                <td>${item.origen}</td>
                <td>${item.proposito}</td>
                <td><span class="badge ${badgeClass}">${item.estado}</span></td>
                <td class="acciones">
                    <button class="btn-editar btn-accion" onclick="editarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                        <span class="tooltip">Editar raza</span>
                    </button>
                    <button class="btn-eliminar btn-accion" onclick="confirmarEliminarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                        <span class="tooltip">Eliminar raza</span>
                    </button>
                </td>
            `;
        } else if (tipo === "corrales") {
            const badgeClass = item.estado === "Activo" ? "badge-activo" : 
                              item.estado === "En mantenimiento" ? "badge-warning" : "badge-inactivo";
            fila.innerHTML = `
                <td><strong>${item.codigo}</strong></td>
                <td>${item.nombre}</td>
                <td><span class="badge badge-config">${item.tipo}</span></td>
                <td>${item.ubicacion}</td>
                <td>${item.capacidad} animales</td>
                <td><span class="badge ${badgeClass}">${item.estado}</span></td>
                <td class="acciones">
                    <button class="btn-editar btn-accion" onclick="editarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                        <span class="tooltip">Editar corral</span>
                    </button>
                    <button class="btn-eliminar btn-accion" onclick="confirmarEliminarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                        <span class="tooltip">Eliminar corral</span>
                    </button>
                </td>
            `;
        } else if (tipo === "grupos") {
            const badgeClass = item.estado === "Activo" ? "badge-activo" : "badge-inactivo";
            fila.innerHTML = `
                <td><strong>${item.codigo}</strong></td>
                <td>${item.nombre}</td>
                <td><span class="badge badge-config">${item.tipo}</span></td>
                <td>${item.descripcion}</td>
                <td>${item.animales} animales</td>
                <td><span class="badge ${badgeClass}">${item.estado}</span></td>
                <td class="acciones">
                    <button class="btn-editar btn-accion" onclick="editarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                        <span class="tooltip">Editar grupo</span>
                    </button>
                    <button class="btn-eliminar btn-accion" onclick="confirmarEliminarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                        <span class="tooltip">Eliminar grupo</span>
                    </button>
                </td>
            `;
        } else if (tipo === "cruces") {
            const badgeClass = item.estado === "Activo" ? "badge-activo" : 
                              item.estado === "En evaluación" ? "badge-warning" : "badge-inactivo";
            fila.innerHTML = `
                <td><strong>${item.codigo}</strong></td>
                <td>${item.razaPadre}</td>
                <td>${item.razaMadre}</td>
                <td>${item.proporcion}</td>
                <td>${item.proposito}</td>
                <td><span class="badge ${badgeClass}">${item.estado}</span></td>
                <td class="acciones">
                    <button class="btn-editar btn-accion" onclick="editarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                        <span class="tooltip">Editar cruce</span>
                    </button>
                    <button class="btn-eliminar btn-accion" onclick="confirmarEliminarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                        <span class="tooltip">Eliminar cruce</span>
                    </button>
                </td>
            `;
        } else if (tipo === "diagnosticos") {
            const badgeClass = item.gravedad === "Alta" ? "badge-danger" : 
                              item.gravedad === "Media" ? "badge-warning" : "badge-info";
            const estadoClass = item.estado === "Activo" ? "badge-activo" : "badge-inactivo";
            fila.innerHTML = `
                <td><strong>${item.codigo}</strong></td>
                <td>${item.nombre}</td>
                <td><span class="badge badge-config">${item.categoria}</span></td>
                <td>${item.sintomas}</td>
                <td><span class="badge ${badgeClass}">${item.gravedad}</span></td>
                <td><span class="badge ${estadoClass}">${item.estado}</span></td>
                <td class="acciones">
                    <button class="btn-editar btn-accion" onclick="editarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-edit"></i> Editar
                        <span class="tooltip">Editar diagnóstico</span>
                    </button>
                    <button class="btn-eliminar btn-accion" onclick="confirmarEliminarConfiguracion(${item.id}, '${tipo}')">
                        <i class="fas fa-trash"></i> Eliminar
                        <span class="tooltip">Eliminar diagnóstico</span>
                    </button>
                </td>
            `;
        }
        
        tbody.appendChild(fila);
    });
    
    // Actualizar contador de registros
    actualizarContadorRegistros(idTabla.replace('Body', ''), datos.length);
}

// Función para actualizar contador de registros
function actualizarContadorRegistros(idTabla, cantidad) {
    let contador = document.getElementById(`contador-${idTabla}`);
    if (!contador) {
        const tablaContainer = document.getElementById(idTabla);
        contador = document.createElement('div');
        contador.id = `contador-${idTabla}`;
        contador.className = 'contador-registros';
        tablaContainer.appendChild(contador);
    }
    contador.innerHTML = `Mostrando <strong>${cantidad}</strong> registros`;
}

// Función para editar una configuración
function editarConfiguracion(id, tipo) {
    // Aquí deberías cargar los datos de la configuración en el modal correspondiente
    switch(tipo) {
        case 'razas':
            abrirModal('modalRazas');
            break;
        case 'corrales':
            abrirModal('modalCorrales');
            break;
        case 'grupos':
            abrirModal('modalGrupos');
            break;
        case 'cruces':
            abrirModal('modalCruces');
            break;
        case 'diagnosticos':
            abrirModal('modalDiagnosticosVeterinarios');
            break;
    }
    
    // En una implementación real, aquí cargarías los datos en el formulario
    console.log(`Editando configuración ${id} de tipo ${tipo}`);
    
    // Simulación de carga de datos en el formulario
    // cargarDatosEnFormularioConfiguracion(id, tipo);
}

// Función para confirmar eliminación de configuración
function confirmarEliminarConfiguracion(id, tipo) {
    registroConfigAEliminar = id;
    tablaConfigAEliminar = tipo;
    
    // Actualizar mensaje del modal de confirmación según el tipo
    const mensaje = document.querySelector('#modalConfirmacion .modal-body p');
    const tipoTexto = getTipoTexto(tipo);
    mensaje.textContent = `¿Está seguro de que desea eliminar este ${tipoTexto}? Esta acción no se puede deshacer.`;
    
    abrirModal('modalConfirmacion');
}

// Función para obtener texto descriptivo del tipo
function getTipoTexto(tipo) {
    const tipos = {
        'razas': 'registro de raza',
        'corrales': 'corral',
        'grupos': 'grupo',
        'cruces': 'cruce',
        'diagnosticos': 'diagnóstico veterinario'
    };
    return tipos[tipo] || 'registro';
}

// Función para eliminar una configuración
function eliminarConfiguracion() {
    if (registroConfigAEliminar && tablaConfigAEliminar) {
        // En una implementación real, aquí harías una llamada a la API para eliminar
        console.log(`Eliminando configuración ${registroConfigAEliminar} de tipo ${tablaConfigAEliminar}`);
        
        // Simulación de eliminación - recargar la tabla
        cargarDatosTablasConfiguracion();
        
        // Cerrar el modal de confirmación
        cerrarModal('modalConfirmacion');
        
        // Mostrar mensaje de éxito
        const tipoTexto = getTipoTexto(tablaConfigAEliminar);
        mostrarMensaje(`${tipoTexto.charAt(0).toUpperCase() + tipoTexto.slice(1)} eliminado correctamente`, "success");
        
        // Resetear variables
        registroConfigAEliminar = null;
        tablaConfigAEliminar = null;
    }
}

// Funciones para guardar configuraciones
function guardarRaza() {
    cargarDatosTablasConfiguracion();
    cerrarModal('modalRazas');
    mostrarMensaje("Raza guardada correctamente", "success");
}

function guardarCorral() {
    cargarDatosTablasConfiguracion();
    cerrarModal('modalCorrales');
    mostrarMensaje("Corral guardado correctamente", "success");
}

function guardarGrupo() {
    cargarDatosTablasConfiguracion();
    cerrarModal('modalGrupos');
    mostrarMensaje("Grupo guardado correctamente", "success");
}

function guardarCruce() {
    cargarDatosTablasConfiguracion();
    cerrarModal('modalCruces');
    mostrarMensaje("Cruce guardado correctamente", "success");
}

function guardarDiagnostico() {
    cargarDatosTablasConfiguracion();
    cerrarModal('modalDiagnosticosVeterinarios');
    mostrarMensaje("Diagnóstico guardado correctamente", "success");
}

// Configuración inicial al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el botón de confirmación de eliminación para configuraciones
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', function() {
            // Determinar qué tipo de eliminación ejecutar
            if (registroConfigAEliminar !== null) {
                eliminarConfiguracion();
            } else if (registroEventoAEliminar !== null) {
                eliminarEvento();
            } else {
                eliminarRegistro();
            }
        });
    }
    
    // Cargar datos iniciales de configuración
    cargarDatosTablasConfiguracion();
    
    // Agregar funcionalidad de búsqueda y filtros
    inicializarFiltrosConfiguracion();
});

// Función para inicializar filtros
function inicializarFiltrosConfiguracion() {
    // Esta función podría agregar filtros dinámicos a las tablas
    console.log("Inicializando filtros de configuración...");
}

// Función para exportar datos de configuración
function exportarConfiguracion(tipo) {
    // Aquí implementarías la lógica para exportar los datos
    const tipoTexto = getTipoTexto(tipo);
    mostrarMensaje(`Exportando datos de ${tipoTexto}...`, "info");
    
    // Simulación de exportación
    setTimeout(() => {
        mostrarMensaje(`Datos de ${tipoTexto} exportados correctamente`, "success");
    }, 1500);
}



//modal cultivos.js

// Manejo del modal de nuevo cultivo
document.addEventListener('DOMContentLoaded', function() {
    const modalNuevoCultivo = document.getElementById('modalNuevoCultivo');
    const btnNuevoCultivo = document.getElementById('btnNuevoCultivo');
    const formNuevoCultivo = document.getElementById('formNuevoCultivo');
    const closeButtons = document.querySelectorAll('.close-modal');

    

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(event) {
        if (event.target === modalNuevoCultivo) {
            modalNuevoCultivo.style.display = 'none';
        }
    });

    // Enviar formulario con AJAX
    if (formNuevoCultivo) {
        formNuevoCultivo.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Enviando formulario...'); // Debug
            
            // Crear FormData directamente del formulario
            const formData = new FormData(this);
            
            // Mostrar datos en consola para debug
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            fetch('agro-cultivo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Respuesta recibida:', response);
                return response.json();
            })
            .then(data => {
                console.log('Datos procesados:', data);
                if (data.success) {
                    alert('Cultivo guardado exitosamente');
                    modalNuevoCultivo.style.display = 'none';
                    formNuevoCultivo.reset();
                    // Recargar la página para mostrar los nuevos datos
                    window.location.reload();
                } else {
                    alert('Error al guardar el cultivo: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                alert('Error al guardar el cultivo. Verifica la consola para más detalles.');
            });
        });
    }
});

// Funciones para editar y eliminar
function editarCultivo(id) {
    alert('Editar cultivo ID: ' + id);
}

function eliminarCultivo(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este cultivo?')) {
        alert('Eliminar cultivo ID: ' + id);
    }
}
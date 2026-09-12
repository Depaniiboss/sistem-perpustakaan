```javascript
const btnTambah = document.getElementById("btnTambah");
const modalTambah = document.getElementById("modalTambah");

const btnClose = document.getElementById("btnClose");
const btnBatal = document.getElementById("btnBatal");

const searchInput = document.getElementById("searchInput");
const table = document.getElementById("peminjamTable");


/* =========================
   BUKA MODAL
========================= */

btnTambah.addEventListener("click", function () {

    modalTambah.classList.add("show");

});


/* =========================
   TUTUP MODAL
========================= */

function tutupModal() {

    modalTambah.classList.remove("show");

}


btnClose.addEventListener("click", tutupModal);

btnBatal.addEventListener("click", tutupModal);


/* =========================
   KLIK LUAR MODAL
========================= */

modalTambah.addEventListener("click", function (event) {

    if (event.target === modalTambah) {

        tutupModal();

    }

});


/* =========================
   ESCAPE
========================= */

document.addEventListener("keydown", function (event) {

    if (event.key === "Escape") {

        tutupModal();

    }

});


/* =========================
   SEARCH PEMINJAM
========================= */

searchInput.addEventListener("keyup", function () {

    const keyword =
        searchInput.value.toLowerCase();

    const rows =
        table.querySelectorAll("tbody tr");


    rows.forEach(function (row) {

        const text =
            row.textContent.toLowerCase();

        if (text.includes(keyword)) {

            row.style.display = "";

        } else {

            row.style.display = "none";

        }

    });

});
```

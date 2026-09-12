const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const container = document.querySelector(".container");

menuBtn.onclick = function(){

    sidebar.classList.toggle("active");

    overlay.classList.toggle("active");

    container.classList.toggle("active");

    if(sidebar.classList.contains("active")){

        menuBtn.innerHTML = "✖";

    }else{

        menuBtn.innerHTML = "☰";

    }

}

overlay.onclick = function(){

    sidebar.classList.remove("active");

    overlay.classList.remove("active");

    container.classList.remove("active");

    menuBtn.innerHTML = "☰";

}

// JavaScript Document
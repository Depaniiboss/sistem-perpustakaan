```javascript
/*
    Mencegah tombol Back browser
*/

history.pushState(null, "", location.href);

window.addEventListener("popstate", function () {

    history.pushState(null, "", location.href);

    location.reload();

});
```

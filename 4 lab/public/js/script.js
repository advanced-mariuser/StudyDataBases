let mask = document.querySelector(".loader");
window.addEventListener("load", () => {
    mask.style.display = 'none';
});

document.addEventListener('DOMContentLoaded', () => {
    let links = document.querySelectorAll('a');

    links.forEach(function (link) {
        link.addEventListener('click', function (event) {
            mask.style.display = 'block';
        });
    });
});

document.querySelector('#phone').onkeydown = function(e){
    inputPhone(e,document.querySelector('#phone'))
}

function inputPhone(e, phone){
    function stop(evt) {
        evt.preventDefault();
    }
    let key = e.key, v = phone.value;
    let not = key.replace(/([0-9])/, 1)

    if(not == 1 || 'Backspace' == not){
        if('Backspace' != not){
            if(v.length < 3 || v ===''){phone.value= '+7('}
            if(v.length === 6){phone.value= v +')'}
            if(v.length === 10){phone.value= v +'-'}
            if(v.length === 13){phone.value= v +'-'}
        }
    }else{stop(e)}  }



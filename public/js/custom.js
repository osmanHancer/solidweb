function changeLang(language, el) {
  var container = document.querySelector('.chooseLang').classList;
  el = el.classList;

  if (container.contains('open')) {
    container.remove('open');
    if (!el.contains('chosen')) {

      document.querySelector('.chooseLang .chosen').classList.remove('chosen');
      el.add('chosen');

      if (language == "tr")
        window.location.href = 'http://google.com';
      // your code

    }
    return;
  }

  container.add('open');

}
window.onload = () => {//bir menü aktifse üst elementini ve bir sonraki üst elementini aktif yapar
  var page_first;
  var page_first_e;
  const page=window.location.href.split('/')[4];
  const id_1 =document.getElementById("page_1");
  const id_2 =document.getElementById("page_2");
  const activeList = document.querySelectorAll("#header .nav .active");
  activeList.forEach((e) => {
    try {
      page_first = e.parentElement.parentElement.parentElement.firstChild.nextSibling.innerText;
      page_first_e = e.parentElement.parentElement.parentElement.firstChild.nextSibling;
      e.parentElement.parentElement.parentElement.firstChild.nextSibling.classList.add("active");
    } catch (error) { }
    try {
      e.parentElement.parentElement.parentElement.parentElement.parentElement.firstChild.nextSibling.classList.add("active")
    } catch (error) { }

  })
  if(page!="about"&&page!="contact"&&page!="privacy-policy"){
id_1.innerHTML=page_first + "&nbsp>";
id_1.parentElement.href=page_first_e.href
  }
  else{
    id_2.innerHTML="";
  }
}
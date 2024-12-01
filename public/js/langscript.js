addEventListener("DOMContentLoaded", (event) => {
    let count=0;
    document.querySelectorAll("body *").forEach(e => {
        let child = e.firstChild;
        let texts = [];
        while (child) {
            if (child.nodeType == 3) {
                texts.push(child.data);
            }
            child = child.nextSibling;
        }
        var text = texts.join("");
        text = text.trim()
        text = text.replaceAll("\n", "");
        text = text.replaceAll("  ", " ");
        if (!text || text.length == 0 || text == "|" || text == "0" || text == "Paul J. Meyer" || text == "Lao Tzu" || text == "Phone:444 12 05" || text == "info@solidelectron.com" ||
            text == "Yıldırım Beyazıt Mah.Aşık Veysel Blv. Teknopoark 5.Bina No:67/4/24 Melikgazi/Kayseri" || text == "IEA,Paris|" || text == "2022" || text == "10 Jan 2023"
            || text == "Solid Electron © 2024." || text == "EN" || text == "TR" || text == "AR" || text == "FR" || text == "RU" || text == "Yıldırım Beyazıt Mah.")
            return;
        text2key= Tr2En(text).replaceAll(/(\r\n\s|\n|\r|\s|-|,|:)/gm, ' ');
        let textList = text2key.replaceAll(/(-|'|©|\.|&|\/)/gm, '').toLocaleLowerCase().split(" ");
        let tkey = "";
        for (let index = 0; index < textList.length; index++) {
            const str = textList[index];
            if(str.length>0) 
            tkey = tkey + (index>2 ? "_"+str[0]:  "_"+ str);
            if (index > 3) break;
        }
        tkey=tkey.replace(/^[^a-z\d]*|[^a-z\d]*$/gi, '');
        console.log(tkey," ====== ",text, ++count);
        saveDb(tkey,text);



        // if(text.length==1)
        //   saveDb(location.href.split("/")[4][0]+"_"+text);
        // else  if(text.length==2)
        //     saveDb(location.href.split("/")[4][0]+"_"+text[0]+text[1]);
        // else  if(text.length==3)
        //     saveDb(location.href.split("/")[4][0]+"_"+text[0]+text[1]+text[2]);
        // else  if(text.length>3)
        //     saveDb(location.href.split("/")[4][0]+"_"+text[0]+text[1]+text[2]+text[3]);



    })

});

function Tr2En(text){

    return text.replaceAll('Ğ','g')
    .replaceAll('Ü','u')
    .replaceAll('Ş','s')
    .replaceAll('I','i')
    .replaceAll('İ','i')
    .replaceAll('Ö','o')
    .replaceAll('Ç','c')
    .replaceAll('ğ','g')
    .replaceAll('ü','u')
    .replaceAll('ş','s')
    .replaceAll('ı','i')
    .replaceAll('ö','o')
    .replaceAll('ç','c');
}
function isNumeric(str) {
    if (typeof str != "string") return false // we only process strings!  
    return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
           !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
  }


async function saveDb(data,text) {
    let json;
    let fd = new FormData();
    fd.append("lkey", data);
    fd.append("ref", "en/"+location.href.split("/")[4])
    fd.append("_action", "insert")
    // fd.append("lval", text)
    try {
        const res = await fetch('/lang/svcKeySave', {
            method: "POST",
            body: fd,
        })
         json = await res.json();
    } catch (err) {
        // showStatus("error", 'sistem hata:' + err.message)
    }
    try { 
        fd = new FormData();
        fd.append("lval", text);
        fd.append("kid", json["data"]["li"]);
        fd.append("lid", 11);
        fd.append("_action", "insert")
        const res = await fetch('/lang/svcDataSave', {
            method: "POST",
            body: fd,
        })
        let restext = await res.text();
    } catch (err) {
        // showStatus("error", 'sistem hata:' + err.message)
    }
}
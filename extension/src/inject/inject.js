let hashes = [];
let APIPATH = "https://pathto/";


chrome.storage.local.get(['hashes'], function (result) {
  hashes = result.hashes || [];
});
var checkNewEmails = function () {
  for (let i = 0; i < hashes.length; i++) {
    $.get(APIPATH+ "api/info/" + hashes[i])
      .done(function (data) {
        let response = JSON.parse(data.trim());
        response = JSON.parse(response);
        if (response.isRead) {
          hashes.splice(i, 1);
          chrome.storage.local.set({ "hashes": hashes }, function () {
            return;
          });
          showNotification(response.link + " has been read at " + response.timestamp);
        }
      });

  }
}

function showNotification(msg) {
  if (!("Notification" in window)) {
    console.log("This browser does not support system notifications");
  }
  else if (Notification.permission === "granted") {
    var notification = new Notification(msg);
  }
  else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {
      if (permission === "granted") {
        var notification = new Notification(msg);
      }
    });
  }
}

var setImg = function () {
  let subject = $('input[name="subjectbox"]')[0].value;
  $.get(APIPATH + "api/email/new/" + subject)
    .done(function (data) {
      console.log(data + " saved");
      $(".editable")[0].innerHTML = $(".editable")[0].innerHTML + '<div ><img class="notfiy" src="' + APIPATH + 'api/check/' + data + '.png"></div>'
      hashes.push(data);
    });
};

chrome.extension.sendMessage({}, function (response) {
  var readyStateCheckInterval = setInterval(function () {
    if (document.readyState === "complete") {
      clearInterval(readyStateCheckInterval);
      setInterval(function () {
        console.log("checking");
        checkNewEmails();
        var notify_url = window.location.href.split("=")[0];
        if (notify_url !== "https://mail.google.com/mail/u/0/#inbox?compose") {
          return;
        }
        if ($('input[name="subjectbox"]')[0].value == "") {
          return;
        }
        if ($(document).find(".notfiy").length > 0) {
          return;
        } else {
          setImg();
        }

      }, 3000);
    }
  }, 10);
});


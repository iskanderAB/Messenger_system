let addDom = (data) => {
    let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    let today = new Date();
    let incoming_msg = document.createElement("div") ;
    let received_msg = document.createElement("div") ;
    let received_withd_msg = document.createElement("div");
    let p = document.createElement("p") ;
    let span = document.createElement("span") ;
    let divImg = document.createElement("div");
    let img = document.createElement("img");
    divImg.classList.add("incoming_msg_img");
    img.alt = "sunil";
    img.src = "https://ptetutorials.com/images/user-profile.png";
    divImg.append(img);
    incoming_msg.classList.add("incoming_msg") ;
    received_msg.classList.add("received_msg");
    received_withd_msg.classList.add("received_withd_msg");
    span.classList.add("time_date");
    p.innerText = data ;
    span.innerHTML =   months[today.getMonth()]+" "+ today.getDate() +" "+ today.getFullYear()+" "+ today.getHours()+":"+today.getMinutes() ;
    received_withd_msg.append(p);
    received_withd_msg.append(span);
    received_msg.append(received_withd_msg);
    incoming_msg.append(divImg);
    incoming_msg.append(received_msg);
    //outPutMessage.append();
    document.getElementById("message").value;
    document.getElementById("listAdd").append(incoming_msg);}
let send =(My_data,url,user)=> {
    axios.post(url, {
        data: My_data,
        friend : user,
})
.then(function (response) {
        if((response.status == 200 || response.status == 2001) && My_data != ""  ){
            let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            let today = new Date();
            let outPutMessage = document.createElement("div") ;
            let sentMessage = document.createElement("div") ;
            let p = document.createElement("p") ;
            let span = document.createElement("span") ;
            outPutMessage.classList.add("outgoing_msg") ;
            sentMessage.classList.add("sent_msg");
            span.classList.add("time_date");
            p.innerText = My_data ;
            span.innerHTML =   months[today.getMonth()]+" "+ today.getDate() +" "+ today.getFullYear()+" "+ today.getHours()+":"+today.getMinutes() ;
            sentMessage.append(p);
            sentMessage.append(span);
            outPutMessage.append(sentMessage);
            outPutMessage.append();
            document.getElementById("message").value;
            document.getElementById("listAdd").append(outPutMessage);
        }

    })
        .catch(function (error) {
            console.log(error);
        });
}
let loadData = (url)=> {
    fetch(url).then(response => {
        const hubUrl = response.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];
        const hub = new URL(hubUrl);
        hub.searchParams.append('topic', 'http://send.com/send');
        const eventSource = new EventSource(hub,{withCredentials:true});
        console.log(eventSource);
        eventSource.onmessage = event => {
            console.log('never givUP') ;
            let data = event.data;
            console.log(data);
            console.log("run");
            addDom(data);
        }
    });
};
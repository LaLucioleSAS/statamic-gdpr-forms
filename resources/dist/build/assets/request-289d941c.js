window.sendRequest=async function(r,e=[]){try{(await fetch(r)).status==200&&window.location.reload()}catch(o){console.error(`Error: ${o}`)}};

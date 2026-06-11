
window.sendRequest = async function(url, params = []){
  try {     
    const response = await fetch(url);
    
    if(response.status == 200){
      window.location.reload(); 
    }
  } catch(err) {
    console.error(`Error: ${err}`);
  }
}
import { async } from "regenerator-runtime";

let allinputquantity = document.querySelectorAll('.inputquantity');
allinputquantity.forEach(inputquantity => {
  console.log(inputquantity)
  inputquantity.addEventListener('input', async function(e) {
      await updateCart(e);
  })
})
async function updateCart(e) {
  let id = e.target.dataset.id;
      let quantity = e.target.value;
      let url = '/cart/update/' ;
      console.log(id);
      console.log(quantity);
      console.log(url);
      let data = {
        quantity: quantity,
        id: id
      }
      fetch(url, {
        method: 'POST',
        body : JSON.stringify(data),
      }).then((response) => {
        return response.json();
      }).then((data) => {
        console.log(data);
      })
      .catch((error) => {
        console.error(error);
      });
  }
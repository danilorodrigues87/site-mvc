
// CHAMA A FUNÇÃO LISTAR AO CARREGAR A PAGINA
$(document).ready(function(){
 listarCursos(null,1,false);

})

function listarCursos(filtro,page=1,ancora) {


 $.ajax({
    url: urlBase+'/cursos',
    method: "post",
    data: {page},
    dataType: "json", 
    success: function(result){

     $('#listar').html(result.itens);
     $('#pagination').html(result.pagination);
     if(ancora){
     ancorarPara('ancora_curso');
   }

},

})
}

function ancorarPara(id) {
    const elemento = document.getElementById(id);
    if (elemento) {
        elemento.scrollIntoView({ 
            behavior: 'smooth', // Faz a rolagem suave
            block: 'start'      // Alinha o topo do elemento ao topo da tela
        });
    }
}
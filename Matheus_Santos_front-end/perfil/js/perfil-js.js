const inputFoto = document.getElementById('input-foto');
const imagemPerfil = document.getElementById('foto-perfil');

inputFoto.addEventListener('change', function() {
  const arquivo = this.files[0];
  if (arquivo) {
    const leitor = new FileReader();
    leitor.onload = function(e) {
      imagemPerfil.src = e.target.result;
    }
    leitor.readAsDataURL(arquivo);
  }
});
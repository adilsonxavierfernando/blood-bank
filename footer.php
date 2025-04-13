<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="shortcut icon" href="images/logotipobpv.png" type="image/x-icon">
    <style>
        .footer {
    background-color: #1f2937; 
    color: #ffffff;
    padding: 40px 20px;
    font-family: 'Arial', sans-serif;
}

.footer h4 {
    font-size: 1.25rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.footer p,
.footer a {
    color: #d1d5db; 
    font-size: 0.9rem;
    margin: 5px 0;
}

.footer a:hover {
    text-decoration: underline;
    color: #f97316;
}

.footer ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.footer ul li {
    margin: 8px 0;
}

.footer ul li a {
    color: #d1d5db;
    text-decoration: none;
}

.footer ul li a:hover {
    color: #f97316;
}

.footer .container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

@media (min-width: 768px) {
    .footer .container {
        grid-template-columns: repeat(3, 1fr);
    }
}

.footer .border-t {
    border-top: 1px solid #374151; 
    margin-top: 20px;
    padding-top: 10px;
}

.footer p.copyright {
    font-size: 0.8rem;
    color: #9ca3af;
    margin-top: 10px;
}
.logo-img {
            height: 50px;
            width: auto;
        }
    </style>
</head>
<body>
<footer class="footer bg-gray-800 text-white py-6">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
       
        <div>
            <h4 class="text-xl font-bold mb-2">Sobre Nós</h4>
            <div class="logo-bloodplace">
            <a href="index.php" class="logo-link-bloodplace">
                <img src="images/logotipobpv.png" alt="Blood Place Voluntary" class="logo-img">
            </a>
        </div>
            <p>Somos uma plataforma dedicada a conectar voluntários para doação de sangue e ajudar na saúde da comunidade.</p>
        </div>

       
        <div>
            <h4 class="text-xl font-bold mb-2">Links Rápidos</h4>
            <ul class="space-y-2">
                <li><a href="index.php" class="hover:underline">Página Inicial</a></li>
                <li><a href="about.php" class="hover:underline">Sobre Nós</a></li>
                <li><a href="contact.php" class="hover:underline">Contato</a></li>
                <li><a href="privacy.php" class="hover:underline">Política de Privacidade</a></li>
            </ul>
        </div>

      
        <div>
            <h4 class="text-xl font-bold mb-2">Contato</h4>
            <p>Email: <a href="mailto:contato@bloodplace.com" class="hover:underline">contato@bloodplace.com</a></p>
            <p>Telefone: +244 933 889 652</p>
            <p>Endereço: Rua Deolinda Ridrigues, 003 - Moxico, Moxico</p>
        </div>
    </div>

  
    <div class="mt-6 border-t border-gray-700 pt-4 text-center">
        <p>&copy; <?php echo date('Y'); ?> Hospital Geral do Moxico- Todos os Direitos Reservados</p>
    
    </div>
</footer>

</body>
</html>
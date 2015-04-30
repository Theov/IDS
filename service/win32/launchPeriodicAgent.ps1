$agentPath = "D:\Developpement\ressources\XAMPP\htdocs\HIDS\src\agent\scanner.php"
$delay = 10

while($true){
    echo "Scan started"
    php $agentPath
    echo "Scan finish"
    Sleep $delay
}

<?php
require_once '../includes/validarsesion.php';
require_once '../config/config.php';

if (!isset($_GET['id'])) {
    exit('ID no proporcionado');
}

$id = (int)$_GET['id'];

try {
    $con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $con->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $mensaje = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$mensaje) {
        exit('Mensaje no encontrado');
    }
    
} catch(PDOException $e) {
    exit('Error de conexión: ' . $e->getMessage());
}

$status_text = [
    'new' => 'Nuevo',
    'read' => 'Leído',
    'replied' => 'Respondido'
];

$status_classes = [
    'new' => 'bg-green-100 text-green-800',
    'read' => 'bg-yellow-100 text-yellow-800',
    'replied' => 'bg-purple-100 text-purple-800'
];
?>

<div class="space-y-4">
    <!-- Información del contacto -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre:</label>
                <p class="text-sm text-gray-900"><?php echo htmlspecialchars($mensaje['name']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email:</label>
                <p class="text-sm text-gray-900">
                    <a href="mailto:<?php echo htmlspecialchars($mensaje['email']); ?>" 
                       class="text-blue-600 hover:text-blue-800">
                        <?php echo htmlspecialchars($mensaje['email']); ?>
                    </a>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha:</label>
                <p class="text-sm text-gray-900"><?php echo date('d/m/Y H:i:s', strtotime($mensaje['created_at'])); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado:</label>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_classes[$mensaje['status']]; ?>">
                    <?php echo $status_text[$mensaje['status']]; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Asunto -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Asunto:</label>
        <div class="bg-white p-3 border border-gray-300 rounded-lg">
            <p class="text-sm text-gray-900"><?php echo htmlspecialchars($mensaje['subject']); ?></p>
        </div>
    </div>

    <!-- Mensaje -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje:</label>
        <div class="bg-white p-4 border border-gray-300 rounded-lg max-h-64 overflow-y-auto">
            <p class="text-sm text-gray-900 whitespace-pre-wrap"><?php echo htmlspecialchars($mensaje['message']); ?></p>
        </div>
    </div>

    <!-- Información técnica -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-sm font-medium text-gray-700 mb-2">Información técnica:</h4>
        <div class="grid grid-cols-1 gap-2 text-xs text-gray-600">
            <div>
                <span class="font-medium">IP:</span> 
                <?php echo htmlspecialchars($mensaje['ip_address'] ?? 'No disponible'); ?>
            </div>
            <div>
                <span class="font-medium">Navegador:</span> 
                <?php echo htmlspecialchars($mensaje['user_agent'] ?? 'No disponible'); ?>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
        <?php if ($mensaje['status'] === 'new'): ?>
            <form method="POST" action="mensajes.php" class="inline">
                <input type="hidden" name="action" value="mark_read">
                <input type="hidden" name="id" value="<?php echo $mensaje['id']; ?>">
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>Marcar como Leído
                </button>
            </form>
        <?php endif; ?>
        
        <?php if ($mensaje['status'] !== 'replied'): ?>
            <form method="POST" action="mensajes.php" class="inline">
                <input type="hidden" name="action" value="mark_replied">
                <input type="hidden" name="id" value="<?php echo $mensaje['id']; ?>">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-reply mr-2"></i>Marcar como Respondido
                </button>
            </form>
        <?php endif; ?>
        
        <a href="mailto:<?php echo htmlspecialchars($mensaje['email']); ?>?subject=Re: <?php echo urlencode($mensaje['subject']); ?>" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-envelope mr-2"></i>Responder por Email
        </a>
    </div>
</div>

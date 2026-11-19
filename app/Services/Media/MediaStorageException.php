<?php

namespace App\Services\Media;

use RuntimeException;

/**
 * A storage provider rejected an upload or a connection check. The message
 * is safe to show to admins (it never contains credentials).
 */
class MediaStorageException extends RuntimeException {}

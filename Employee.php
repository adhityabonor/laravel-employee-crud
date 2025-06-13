namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'nomor',
        'nama',
        'jabatan',
        'talahir',
        'photo_upload_path',
        'created_on',
        'updated_on',
        'created_by',
        'updated_by',
        'deleted_on',
    ];

    public $timestamps = false;
}

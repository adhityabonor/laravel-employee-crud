namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redis;
use App\Models\Employee;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor' => 'required|unique:employees,nomor',
            'nama' => 'required',
            'photo' => 'nullable|file|image',
        ]);

        // Upload photo to S3
        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $photoUrl = Storage::disk('s3')->url($path);
        }

        $employee = Employee::create([
            'nomor' => $request->nomor,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'talahir' => $request->talahir,
            'photo_upload_path' => $photoUrl,
            'created_on' => now(),
            'created_by' => auth()->user()->name ?? 'system'
        ]);

        // Simpan ke Redis
        Redis::set("emp_{$employee->nomor}", $employee->toJson());

        return response()->json($employee, 201);
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $employee->fill($request->except('photo'));

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $employee->photo_upload_path = Storage::disk('s3')->url($path);
        }

        $employee->updated_on = now();
        $employee->updated_by = auth()->user()->name ?? 'system';

        $employee->save();

        // Update Redis
        Redis::set("emp_{$employee->nomor}", $employee->toJson());

        return response()->json($employee);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $nomor = $employee->nomor;

        $employee->delete();

        // Hapus dari Redis
        Redis::del("emp_{$nomor}");

        return response()->json(['message' => 'Deleted']);
    }
}

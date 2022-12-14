<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
use Illuminate\Support\Facades\Auth; // ここを追加
use App\Http\Requests\StoreDatasRequest; // ここを追加
use Illuminate\Support\Facades\DB; // ここを追加
use Illuminate\Support\Facades\Log; // ここを追加
use App\Http\Requests\DataUpdateRequest;

class ProjectController extends Controller
{
    //
    public function home(){
        return view('projects.home');
    }

    public function record(){
        $datas = Auth::user()->datas->all();
        return view('projects.index',compact('datas'));
    }

    public function store(StoreDatasRequest $request)
    {
        DB::beginTransaction();
        try{
            $datas = Data::create([
                'bt' => $request->bt,
                'pulse' => $request->pulse,
                'Trb_bw' => $request->Trb_bw,
                'Tra_bw' => $request->Tra_bw,
                'fatigue' => $request->fatigue,
                'user_id' => Auth::id(),
            ]);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::debug($e);
            abort(500);
        }
        return redirect()->route('record.index');
    }

    public function showData($id)
    {
        // 渡されてきた記事IDのデータを取得
        $datas = Data::find($id);

        return view('projects.detail', compact('datas'));
    }

    public function record_update($id)
    {
        // 渡されてきた記事IDのデータを取得
        $datas = Data::find($id);

        return view('projects.update', compact('datas'));
    }

    public function update_store(DataUpdateRequest $request, $id)
    {
        
        // 渡されてきた記事IDのデータを取得
        $datas = Data::find($id);

         // トランザクション開始
         DB::beginTransaction();
        // 編集する内容をfillメソッドを使用して記述
        try{
                $datas->fill([
                'bt' => $request->bt,
                'pulse' => $request->pulse,
                'Trb_bw' => $request->Trb_bw,
                'Tra_bw' => $request->Tra_bw,
                'fatigue' => $request->fatigue,
            ]);

            // 保存処理
            $datas->save();
            DB::commit();
        }
        catch(\Exception $e) {
            // トランザクションロールバック
            DB::rollBack();

            // ログ出力
            Log::debug($e);

            // エラー画面遷移
            abort(500);
        }
            return redirect()->route('record.index', $datas->id);
            
        }
    /**
     * 記事削除処理
     */
    public function record_delete($id)
    {
        DB::beginTransaction();
        try{
            // 渡されてきた記事IDのデータを取得
            $datas = Data::find($id);

            // 記事削除処理
            $datas->delete();

            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            Log::debug($e);

            abort(500);
        }

        return redirect()->route('record.index');
    }
}
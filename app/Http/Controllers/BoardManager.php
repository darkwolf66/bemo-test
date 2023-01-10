<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Column;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardManager extends Controller
{
    /**
     * Used to get the entire Board
     * @param Request $request
     * @param $boardId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getBoard(Request $request, $boardId){
        $board = Auth::user()->boards()->where('id', $boardId)->with(['columns', 'columns.cards']);
        if($board->count() <= 0){
            return redirect('/404');
        }
        return response()->json($board->first());
    }

    /**
     * Used to list the boards
     * @return \Illuminate\Http\JsonResponse
     */
    public function myBoards(){
        return response()->json(
            Auth::user()->boards()->get()
        );
    }

    /**
     * Used to update the cards order and information
     * @param Request $request
     * @param $boardId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateCards(Request $request, $boardId){
        $board = Auth::user()->boards()->where('id', $boardId)->with(['columns', 'columns.cards']);
        if($board->count() <= 0){
            return redirect('/404');
        }
        $columns = $request->all();

        foreach ($columns['columns'] as $column){
            $cardPosition = 0;
            foreach ($column['cards'] as $newCard){
                $cardPosition++;
                $card = Auth::user()->cards()->where('id', $newCard['id']);
                if($card->count() <= 0){
                    continue;
                }
                $card = $card->first();
                $card->order = $cardPosition;
                $card->column_id = $column['id'];
                $card->title = $newCard['title'];
                $card->description = $newCard['description'];
                $card->save();
            }
        }
        return response()->json($columns);
    }

    /**
     * Used to update a single card
     * @param Request $request
     * @param $boardId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateCard(Request $request, $boardId){
        $board = Auth::user()->boards()->where('id', $boardId)->with(['columns', 'columns.cards']);
        if($board->count() <= 0){
            return redirect('/404');
        }
        $newCard = $request->all();
        $card = Auth::user()->cards()->where('id', $newCard['id']);
        if($card->count() <= 0){
            return redirect('/404');
        }
        $card = $card->first();
        $card->title = $newCard['title'];
        $card->description = $newCard['description'];
        $card->save();
        return response()->json($card);
    }

    /**
     * Used to create new columns
     * @param Request $request
     * @param $boardId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createColumn(Request $request, $boardId){
        $board = Auth::user()->boards()->where('id', $boardId)->with(['columns', 'columns.cards']);
        if($board->count() <= 0){
            return redirect('/404');
        }
        $newColumnTitle = $request->all()['title'];

        $lastOrderNumber = $board->first()->columns()->max('order');
        Column::insert(
            [
                'order' => $lastOrderNumber+1,
                'title' => $newColumnTitle,
                'board_id' => $boardId,
                'user_id' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
        return response()->json('success');
    }

    /**
     * Used to delete columns
     * @param Request $request
     * @param $boardId
     * @param $columnId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteColumn(Request $request, $boardId, $columnId){
        $board = Auth::user()->boards()->where('id', $boardId)->with(['columns', 'columns.cards']);
        if($board->count() <= 0){
            return redirect('/404');
        }
        $board = $board->first();

        $column = $board->columns()->where('id', $columnId);
        $column->delete();

        return response()->json('success');
    }

    /**
     * Used to add new card
     * @param Request $request
     * @param $boardId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function newCard(Request $request, $boardId){
        $board = Auth::user()->boards()->where('id', $boardId)->with(['columns', 'columns.cards']);
        if($board->count() <= 0){
            return redirect('/404');
        }
        $board = $board->first();

        $newCard = $request->all();

        $column = $board->first()->columns()->where('id', $newCard['column_id'])->first();

        $maxOrderOnCards = $column->cards()->max('order');

        Card::insert(
            [
                'order' => $maxOrderOnCards+1,
                'title' => $newCard['title'],
                'description' => $newCard['description'],
                'column_id' => $column->id,
                'board_id' => $boardId,
                'user_id' => Auth::id()
            ]
        );
        return response()->json('success');
    }

    /**
     * Used to list cards, deleted cards and date filtered cards
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listCards(Request $request){
        $card = Card::select();
        if($request->input('status') == 0){
            $card = $card->withTrashed();
        }

        if(!empty($request->input('date'))){
            $date = Carbon::parse($request->input('date'));
            $card = $card->whereBetween('created_at', [$date->startOfDay()->toDateTimeString(), $date->endOfDay()->toDateTimeString()]);
        }

        return response()->json($card->get());
    }
}

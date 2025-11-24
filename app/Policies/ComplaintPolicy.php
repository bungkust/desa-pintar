<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ComplaintPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // super_admin, admin_desa, lurah can view all
        if (in_array($user->role, ['super_admin', 'admin_desa', 'lurah'])) {
            return true;
        }

        // petugas can view assigned complaints only (handled in controller)
        if ($user->isPetugas()) {
            return true;
        }

        // viewer can view (but will see limited data in controller)
        if ($user->isViewer()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Complaint $complaint): bool
    {
        // super_admin, admin_desa, lurah can view all
        if (in_array($user->role, ['super_admin', 'admin_desa', 'lurah'])) {
            return true;
        }

        // petugas can only view assigned complaints
        if ($user->isPetugas()) {
            return $complaint->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create complaints manually
        // Public submission doesn't require authentication
        return in_array($user->role, ['super_admin', 'admin_desa', 'lurah']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Complaint $complaint): bool
    {
        // super_admin, admin_desa can update all
        if (in_array($user->role, ['super_admin', 'admin_desa'])) {
            return true;
        }

        // lurah can update but not assign
        if ($user->isLurah()) {
            return true;
        }

        // petugas can only update assigned complaints
        if ($user->isPetugas()) {
            return $complaint->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Complaint $complaint): bool
    {
        // Only super_admin and admin_desa can delete
        return in_array($user->role, ['super_admin', 'admin_desa']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Complaint $complaint): bool
    {
        // Only super_admin and admin_desa can restore
        return in_array($user->role, ['super_admin', 'admin_desa']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Complaint $complaint): bool
    {
        // Only super_admin can force delete
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can assign petugas.
     */
    public function assignPetugas(User $user, Complaint $complaint): bool
    {
        // Only super_admin and admin_desa can assign
        return in_array($user->role, ['super_admin', 'admin_desa']);
    }

    /**
     * Determine whether the user can change status.
     */
    public function changeStatus(User $user, Complaint $complaint, string $newStatus): bool
    {
        // super_admin, admin_desa can change to any status
        if (in_array($user->role, ['super_admin', 'admin_desa'])) {
            return $this->isValidStatusTransition($complaint->status, $newStatus);
        }

        // lurah can change status (but not assign)
        if ($user->isLurah()) {
            return $this->isValidStatusTransition($complaint->status, $newStatus);
        }

        // petugas can only change status for assigned complaints with limited transitions
        if ($user->isPetugas() && $complaint->assigned_to === $user->id) {
            return $this->isValidPetugasStatusTransition($complaint->status, $newStatus);
        }

        return false;
    }

    /**
     * Validate status transition
     */
    protected function isValidStatusTransition(string $from, string $to): bool
    {
        $validTransitions = [
            'backlog' => ['verification', 'rejected'],
            'verification' => ['todo', 'backlog', 'rejected'],
            'todo' => ['in_progress', 'verification', 'rejected'],
            'in_progress' => ['done', 'todo', 'rejected'],
            'done' => [], // Final state
            'rejected' => ['backlog', 'verification'], // Can be reopened
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }

    /**
     * Validate petugas status transition (limited)
     */
    protected function isValidPetugasStatusTransition(string $from, string $to): bool
    {
        // Petugas can only: todo -> in_progress -> done
        $petugasTransitions = [
            'todo' => ['in_progress'],
            'in_progress' => ['done'],
        ];

        return in_array($to, $petugasTransitions[$from] ?? []);
    }

    /**
     * Determine whether the user can view private data (name, phone, address).
     */
    public function viewPrivateData(User $user, Complaint $complaint): bool
    {
        // super_admin, admin_desa, lurah can view private data
        return in_array($user->role, ['super_admin', 'admin_desa', 'lurah']);
    }

    /**
     * Determine whether the user can view warga comments.
     */
    public function viewWargaComments(User $user, Complaint $complaint): bool
    {
        // Petugas cannot see warga comments
        if ($user->isPetugas()) {
            return false;
        }

        // Others can view
        return in_array($user->role, ['super_admin', 'admin_desa', 'lurah']);
    }

    /**
     * Determine whether the user can add comments.
     */
    public function addComment(User $user, Complaint $complaint): bool
    {
        // All authenticated users with access can comment
        return $this->view($user, $complaint);
    }

    /**
     * Determine whether the user can export PDF.
     */
    public function exportPDF(User $user): bool
    {
        // Only admins can export
        return in_array($user->role, ['super_admin', 'admin_desa', 'lurah']);
    }

    /**
     * Determine whether the user can view dashboard statistics.
     */
    public function viewDashboard(User $user): bool
    {
        // All roles except viewer can see some statistics
        // Viewer can only see basic stats (handled in controller)
        return true;
    }
}

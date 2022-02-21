<?php

namespace Uelnur\SymfonyViewController;

interface ViewFirewallInterface {
    public function viewIsGranted(BaseViewContext $viewContext): ?bool;
    public function viewIfNotGranted(BaseViewContext $viewContext): void;
}

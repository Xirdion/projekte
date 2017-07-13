import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { AppComponent } from './app.component';
import { ServerComponent } from './server/server.component';
import { ServersComponent } from './servers/servers.component';
import { AlertComponent } from './alert/alert.component';
import { AlertSuccessComponent } from './alert/alert-success/alert-success.component';
import { AlertWarningComponent } from './alert/alert-warning/alert-warning.component';

@NgModule({
  declarations: [
      AppComponent,
      ServerComponent,
      ServersComponent,
      AlertComponent,
      AlertSuccessComponent,
      AlertWarningComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
